<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');


// =========================================================
// FUNCIONES
// =========================================================

function responderError(
    string $mensaje,
    int $codigo = 400
): never {

    http_response_code($codigo);

    echo json_encode([
        'encontrado' => false,
        'message' => $mensaje
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


function limpiarTexto(?string $texto): string
{
    if ($texto === null) {
        return '';
    }

    return trim(
        preg_replace('/\s+/u', ' ', $texto)
    );
}


function normalizarEtiqueta(string $texto): string
{
    $texto = mb_strtolower(
        limpiarTexto($texto),
        'UTF-8'
    );

    return strtr($texto, [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'ü' => 'u'
    ]);
}


// =========================================================
// 1. RECIBIMOS LOS DATOS DE NUESTRO FRONTEND
// =========================================================

$rawInput = file_get_contents('php://input');

$data = json_decode($rawInput, true);


if (!is_array($data)) {
    responderError('Solicitud inválida.');
}


$dni = trim((string) ($data['dni'] ?? ''));

$sexo = strtoupper(
    trim((string) ($data['sexo'] ?? ''))
);


// =========================================================
// 2. VALIDACIÓN
// =========================================================

if (!preg_match('/^\d{7,8}$/', $dni)) {

    responderError(
        'El DNI ingresado no es válido.',
        422
    );
}


if (!in_array($sexo, ['M', 'F'], true)) {

    responderError(
        'La opción de sexo no es válida.',
        422
    );
}


// =========================================================
// 3. CONSULTA A LA FUENTE
// =========================================================

$url = 'https://abogadosxargentina.com.ar/consulta-el-padron/';


$postData = http_build_query([
    'dni'  => $dni,
    'sexo' => $sexo
]);


$ch = curl_init($url);


curl_setopt_array($ch, [

    CURLOPT_POST => true,

    CURLOPT_POSTFIELDS => $postData,

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_FOLLOWLOCATION => true,

    CURLOPT_MAXREDIRS => 3,

    CURLOPT_CONNECTTIMEOUT => 5,

    CURLOPT_TIMEOUT => 10,

    CURLOPT_ENCODING => '',

    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: text/html,application/xhtml+xml'
    ],

    CURLOPT_USERAGENT =>
        'ConsultaPadron/1.0'

]);


$html = curl_exec($ch);


if ($html === false) {

    curl_close($ch);

    responderError(
        'No pudimos comunicarnos con la fuente de consulta.',
        502
    );
}


$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);


curl_close($ch);


if ($httpCode < 200 || $httpCode >= 400) {

    responderError(
        'La fuente de consulta no se encuentra disponible.',
        502
    );
}


// =========================================================
// 4. INTERPRETAMOS EL HTML RECIBIDO
// =========================================================

libxml_use_internal_errors(true);

$dom = new DOMDocument();

$cargado = $dom->loadHTML(
    '<?xml encoding="UTF-8">' . $html,
    LIBXML_NOERROR | LIBXML_NOWARNING
);

libxml_clear_errors();


if (!$cargado) {

    responderError(
        'No pudimos interpretar la respuesta recibida.',
        502
    );
}


$xpath = new DOMXPath($dom);


// Buscamos específicamente:
//
// <div id="padron">
//     ...
// </div>

$padron = $xpath
    ->query('//*[@id="padron"]')
    ->item(0);


if (!$padron) {

    responderError(
        'No encontramos resultados para los datos ingresados.',
        404
    );
}


// =========================================================
// 5. EXTRAEMOS LOS CAMPOS
// =========================================================

$resultado = [
    'apellidoNombre'    => '',
    'colegio'           => '',
    'direccionVotacion' => ''
];


$etiquetas = $xpath->query(
    './/h4',
    $padron
);


foreach ($etiquetas as $etiqueta) {

    $nombreEtiqueta = normalizarEtiqueta(
        $etiqueta->textContent
    );


    $valorNode = $xpath
        ->query(
            'following-sibling::h5[1]',
            $etiqueta
        )
        ->item(0);


    if (!$valorNode) {
        continue;
    }


    $valor = limpiarTexto(
        $valorNode->textContent
    );


    if (str_contains(
        $nombreEtiqueta,
        'elector'
    )) {

        $resultado['apellidoNombre'] = $valor;

    }


    elseif (str_contains(
        $nombreEtiqueta,
        'colegio'
    )) {

        $resultado['colegio'] = $valor;

    }


    elseif (
        str_contains(
            $nombreEtiqueta,
            'donde vot'
        )
    ) {

        $resultado['direccionVotacion'] = $valor;

    }

}


// =========================================================
// 6. VERIFICAMOS QUE HAYA UN RESULTADO COMPLETO
// =========================================================

if (
    $resultado['apellidoNombre'] === '' ||
    $resultado['colegio'] === '' ||
    $resultado['direccionVotacion'] === ''
) {

    responderError(
        'No encontramos resultados para los datos ingresados.',
        404
    );
}


// =========================================================
// 7. DEVOLVEMOS ÚNICAMENTE LOS DATOS NECESARIOS
// =========================================================

echo json_encode([
    'encontrado'        => true,
    'apellidoNombre'    => $resultado['apellidoNombre'],
    'colegio'           => $resultado['colegio'],
    'direccionVotacion' => $resultado['direccionVotacion']
], JSON_UNESCAPED_UNICODE);