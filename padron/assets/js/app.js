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


        <button
            type="button"
            class="new-search"
            id="new-search"
        >
            ← Nueva consulta
        </button>
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