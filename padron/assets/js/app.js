const form = document.getElementById('padron-form');
const dniInput = document.getElementById('dni');
const submitButton = document.getElementById('submit-button');
const statusBox = document.getElementById('status');
const resultadoBox = document.getElementById('resultado');


form.addEventListener('submit', async (event) => {

    event.preventDefault();

    limpiarResultado();

    const dni = dniInput.value.trim();
    const sexo = form.querySelector(
        'input[name="sexo"]:checked'
    )?.value;


    // =====================================================
    // VALIDACIÓN
    // =====================================================

    if (!/^\d{7,8}$/.test(dni)) {

        mostrarError(
            'Ingresá un DNI válido, sin puntos ni espacios.'
        );

        return;
    }


    if (!['M', 'F'].includes(sexo)) {

        mostrarError(
            'Seleccioná una opción de sexo.'
        );

        return;
    }


    // =====================================================
    // INICIAMOS BÚSQUEDA
    // =====================================================

    setCargando(true);


    try {

        const response = await fetch(
            'api/consultar-padron.php',
            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },

                body: JSON.stringify({
                    dni: dni,
                    sexo: sexo
                })
            }
        );


        const data = await response.json();


        // Terminó la comunicación.
        // Sacamos el loader antes de mostrar cualquier cosa.

        setCargando(false);


        if (!response.ok) {

            mostrarError(
                data.message ||
                'No pudimos realizar la consulta.'
            );

            return;
        }


        if (!data.encontrado) {

            mostrarError(
                data.message ||
                'No encontramos resultados para los datos ingresados.'
            );

            return;
        }


        // =================================================
        // RESULTADO CORRECTO
        // =================================================

        mostrarResultado(data);


    } catch (error) {

        console.error(error);

        setCargando(false);

        mostrarError(
            'No pudimos realizar la consulta en este momento. Intentá nuevamente.'
        );

    }

});


// =========================================================
// ESTADO DE CARGA
// =========================================================

function setCargando(cargando) {

    submitButton.disabled = cargando;


    if (cargando) {

        submitButton.textContent = 'Consultando…';

        statusBox.innerHTML = `
            <div class="searching">

                <div class="searching-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <div class="searching-copy">

                    <strong>
                        Consultando el padrón
                    </strong>

                    <span>
                        Estamos buscando tu lugar de votación…
                    </span>

                </div>

            </div>
        `;

        statusBox.classList.add('visible');

    } else {

        submitButton.textContent =
            'Consultar lugar de votación';

        statusBox.classList.remove('visible');

        statusBox.innerHTML = '';

    }

}


// =========================================================
// MOSTRAR RESULTADO
// =========================================================

function mostrarResultado(data) {

    // Ocultamos el formulario.
    // La card pasa del estado "consulta" al estado "resultado".

    form.hidden = true;


    resultadoBox.innerHTML = `

        <div class="result-header">

            <span class="result-check">
                ✓
            </span>

            <div>

                <span class="result-kicker">
                    Consulta realizada
                </span>

                <h2>
                    Encontramos tu lugar de votación
                </h2>

            </div>

        </div>


        <div class="result-data">


            <div class="result-item result-person">

                <span>
                    Elector/a
                </span>

                <strong>
                    ${escapeHTML(data.apellidoNombre)}
                </strong>

            </div>


            <div class="result-grid">


                <div class="result-item">

                    <span>
                        Colegio
                    </span>

                    <strong>
                        ${escapeHTML(data.colegio)}
                    </strong>

                </div>


                <div class="result-item">

                    <span>
                        Dónde votás
                    </span>

                    <strong>
                        ${escapeHTML(data.direccionVotacion)}
                    </strong>

                </div>


            </div>

        </div>


        <div class="result-actions">

            <button
                type="button"
                class="pdf-button"
                id="download-pdf"
            >
                Descargar constancia (PDF)
            </button>

            <button
                type="button"
                class="new-search"
                id="new-search"
            >
                Hacer otra consulta
            </button>

        </div>
    `;


    resultadoBox.hidden = false;


    document
        .getElementById('new-search')
        .addEventListener('click', () => {

            form.reset();

            limpiarResultado();

            form.hidden = false;

            dniInput.focus();

        });

        document
            .getElementById('download-pdf')
            .addEventListener('click', () => {

                descargarConstancia({
                    dni: dniInput.value.trim(),
                    apellidoNombre: data.apellidoNombre,
                    colegio: data.colegio,
                    direccionVotacion: data.direccionVotacion
                });

            });

}


// =========================================================
// ERRORES
// =========================================================

function mostrarError(mensaje) {

    statusBox.textContent = mensaje;

    statusBox.classList.add('visible');

}


// =========================================================
// LIMPIAR RESULTADO
// =========================================================

function limpiarResultado() {

    resultadoBox.hidden = true;

    resultadoBox.innerHTML = '';

    statusBox.innerHTML = '';

    statusBox.classList.remove('visible');

}


// =========================================================
// SEGURIDAD
// Evitamos insertar HTML proveniente de la fuente externa.
// =========================================================

function escapeHTML(valor) {

    const div = document.createElement('div');

    div.textContent = valor ?? '';

    return div.innerHTML;

}

// =========================================================
// DESCARGAR CONSTANCIA PDF
// =========================================================

function descargarConstancia(datos) {

    // jsPDF fue cargado antes que app.js en index.php
    const { jsPDF } = window.jspdf;

    const doc = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4'
    });


    // =====================================================
    // FECHA
    // =====================================================

    const fecha = new Intl.DateTimeFormat('es-AR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(new Date());


    // =====================================================
    // COLORES
    // =====================================================

    const azul = [24, 44, 61];
    const gris = [95, 95, 95];
    const negro = [25, 25, 25];
    const celeste = [32, 184, 223];


    // =====================================================
    // TÍTULO
    // =====================================================

    doc.setTextColor(...azul);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(18);

    doc.text(
        'Constancia de lugar de votación',
        20,
        28
    );


    // Línea celeste debajo del título

    doc.setDrawColor(...celeste);
    doc.setLineWidth(0.7);

    doc.line(
        20,
        32,
        82,
        32
    );


    // =====================================================
    // INFORMACIÓN GENERAL
    // =====================================================

    doc.setTextColor(...gris);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);

    doc.text(
        'Elecciones del Consejo de la Magistratura de la Nación',
        20,
        43
    );

    doc.text(
        `Emitido el ${fecha}`,
        20,
        49
    );


    // =====================================================
    // FUNCIÓN AUXILIAR PARA LOS DATOS
    // =====================================================

    function agregarDato(label, valor, y) {

        doc.setTextColor(...azul);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(9);

        doc.text(
            label.toUpperCase(),
            20,
            y
        );


        doc.setTextColor(...negro);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(11);

        doc.text(
            String(valor || '-'),
            20,
            y + 6
        );

    }


    // =====================================================
    // DATOS DEL RESULTADO
    // =====================================================

    agregarDato(
        'Elector/a',
        datos.apellidoNombre,
        67
    );

    agregarDato(
        'Documento',
        datos.dni,
        83
    );

    agregarDato(
        'Colegio',
        datos.colegio,
        99
    );

    agregarDato(
        'Dónde votás',
        datos.direccionVotacion,
        115
    );


    // =====================================================
    // PIE DE PÁGINA
    // =====================================================

    doc.setTextColor(125, 125, 125);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7);

    doc.text(
        'Documento informativo, sin validez oficial. Herramienta independiente de consulta electoral.',
        20,
        278
    );

    doc.text(
        'Los datos personales son tratados conforme a la Ley Nº 25.326 de Protección de los Datos Personales.',
        20,
        283
    );


    // =====================================================
    // DESCARGA
    // =====================================================

    doc.save('constancia-lugar-votacion.pdf');

}