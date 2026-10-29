<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');header('Cache-Control: no-store');header('X-Content-Type-Options: nosniff');
function responderError(string $message,int $code=400): never {http_response_code($code);echo json_encode(['encontrado'=>false,'message'=>$message],JSON_UNESCAPED_UNICODE);exit;}
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Allow: POST');responderError('Usá el formulario de consulta.',405);}
$raw=file_get_contents('php://input',false,null,0,2049);if(strlen($raw)>2048)responderError('Solicitud demasiado extensa.',413);
$data=json_decode($raw,true);if(!is_array($data))responderError('Solicitud inválida.');
$tomo=is_string($data['tomo'] ?? null)?trim($data['tomo']):'';$folio=is_string($data['folio'] ?? null)?trim($data['folio']):'';
if(!preg_match('/^[0-9]{1,3}$/D',$tomo) || (int)$tomo<1 || !preg_match('/^[0-9]{1,4}$/D',$folio) || (int)$folio<1)responderError('Ingresá un tomo y un folio válidos, solo con números.',422);
require_once __DIR__.'/cpacf.php';
try {$result=cpacfLookup((string)(int)$tomo,(string)(int)$folio);}catch(Throwable $e){responderError('No pudimos comunicarnos con el padrón del CPACF. Intentá nuevamente o consultá en la fuente original.',502);}
if($result['status']==='not_found')responderError('No encontramos esa matrícula en el padrón provisorio. Revisá el tomo y el folio.',404);
if($result['status']!=='found')responderError('El CPACF respondió con un formato que no pudimos interpretar. Podés realizar la consulta en la fuente original.',502);
echo json_encode(['encontrado'=>true,'fields'=>$result['fields']],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
