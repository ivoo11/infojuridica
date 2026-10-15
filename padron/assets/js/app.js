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
            .addEventListener('click', async () => {
                const button = document.getElementById('download-pdf');
                button.disabled = true; button.textContent = 'Preparando PDF…';
                try { await descargarConstancia({
                    dni: dniInput.value.trim(),
                    apellidoNombre: data.apellidoNombre,
                    colegio: data.colegio,
                    direccionVotacion: data.direccionVotacion
                }); } catch (e) { mostrarError('No pudimos generar el PDF. Intentá nuevamente.'); }
                finally { button.disabled = false; button.textContent = 'Descargar constancia (PDF)'; }
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

async function descargarConstancia(datos) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4', compress: true });
    const ink = [23, 23, 23], burgundy = [113, 27, 45], gray = [102, 102, 102], rule = [221, 217, 209], paper = [245, 243, 238];
    const fecha = new Intl.DateTimeFormat('es-AR', { day: 'numeric', month: 'long', year: 'numeric', timeZone: 'America/Argentina/Buenos_Aires' }).format(new Date());
    let logo = null;
    try {
        logo = await new Promise((resolve, reject) => {
            const image = new Image(); image.onload = () => resolve(image); image.onerror = reject;
            image.src = '/padron/assets/img/logo-infojuridica.png';
        });
    } catch (e) { /* A text masthead keeps the PDF usable if the image cannot load. */ }
    function header(continued = false) {
        if (logo) doc.addImage(logo, 'PNG', 20, 20, 57, 57 * logo.naturalHeight / logo.naturalWidth);
        else { doc.setFont('times', 'bold'); doc.setFontSize(20); doc.setTextColor(...ink); doc.text('INFO JURÍDICA', 20, 31); }
        doc.setFont('helvetica', 'normal'); doc.setFontSize(8); doc.setTextColor(...gray);
        doc.text('CONSULTA DEL PADRÓN', 190, 26, { align: 'right' });
        doc.text(fecha, 190, 32, { align: 'right' });
        doc.setDrawColor(...rule); doc.setLineWidth(.3); doc.line(20, 48, 190, 48);
        doc.setTextColor(...burgundy); doc.setFont('helvetica', 'bold'); doc.setFontSize(9);
        doc.text('ELECCIONES DE LA ABOGACÍA', 20, 61);
        doc.setTextColor(...ink); doc.setFont('times', 'normal'); doc.setFontSize(28);
        doc.text(continued ? 'Datos de la consulta' : 'Tu lugar de votación', 20, 75);
        doc.setFont('helvetica', 'normal'); doc.setFontSize(10); doc.setTextColor(...gray);
        doc.text('Consejo de la Magistratura de la Nación', 20, 85);
    }
    let y = 101;
    header();
    function nextPage() { doc.addPage(); header(true); y = 101; }
    function field(label, value, highlight = false, fontSize = 13) {
        doc.setFont('helvetica', 'normal'); doc.setFontSize(fontSize);
        const inset = highlight ? 8 : 0;
        const lines = doc.splitTextToSize(String(value || '-').replace(/\s+/g, ' ').trim(), 170 - inset * 2);
        const lineHeight = fontSize * .3528 * 1.35;
        const fullHeight = 14 + lines.length * lineHeight + (highlight ? 8 : 0);
        if (y + fullHeight > 255 && fullHeight <= 154) nextPage();
        let offset = 0;
        while (offset < lines.length) {
            if (y + 14 + lineHeight + (highlight ? 8 : 0) > 255) nextPage();
            const count = Math.max(1, Math.floor((255 - y - 14 - (highlight ? 8 : 0)) / lineHeight));
            const chunk = lines.slice(offset, offset + count);
            const height = 14 + chunk.length * lineHeight + (highlight ? 8 : 0);
            if (highlight) { doc.setFillColor(...paper); doc.rect(20, y - 4, 170, height, 'F'); doc.setFillColor(...burgundy); doc.rect(20, y - 4, 1, height, 'F'); }
            doc.setFont('helvetica', 'bold'); doc.setFontSize(8.5); doc.setTextColor(...burgundy);
            doc.text(label.toUpperCase() + (offset ? ' (CONTINUACIÓN)' : ''), 20 + inset, y + (highlight ? 4 : 0));
            doc.setFont('helvetica', 'normal'); doc.setFontSize(fontSize); doc.setTextColor(...ink);
            doc.text(chunk, 20 + inset, y + 8 + (highlight ? 4 : 0), { lineHeightFactor: 1.35 });
            y += height + 5; offset += chunk.length;
            if (offset < lines.length) nextPage();
        }
    }
    field('Elector/a', datos.apellidoNombre, false, 16);
    field('Documento', datos.dni, false, 12);
    field('Colegio', datos.colegio);
    field('Dónde votás', datos.direccionVotacion, true, 15);
    const pages = doc.getNumberOfPages();
    for (let page = 1; page <= pages; page++) {
        doc.setPage(page); doc.setDrawColor(...rule); doc.setLineWidth(.3); doc.line(20, 266, 190, 266);
        doc.setFont('helvetica', 'normal'); doc.setFontSize(8); doc.setTextColor(...gray);
        doc.text('Documento informativo, sin validez oficial.', 20, 273);
        doc.text('Fuente de los datos: Abogados por Argentina.', 20, 278);
        doc.setTextColor(...burgundy); doc.textWithLink('infojuridica.com.ar', 20, 286, { url: 'https://infojuridica.com.ar' });
        doc.setTextColor(...gray); doc.text(`${page} / ${pages}`, 190, 286, { align: 'right' });
    }
    doc.setProperties({ title: 'Consulta de lugar de votación - INFO JURÍDICA', subject: 'Consulta informativa del padrón electoral', author: 'INFO JURÍDICA' });
    doc.save('infojuridica-lugar-de-votacion.pdf');
}
