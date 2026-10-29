const form = document.getElementById('padron-form');
const tomoInput = document.getElementById('tomo');
const folioInput = document.getElementById('folio');
const submitButton = document.getElementById('submit-button');
const statusBox = document.getElementById('status');
const resultadoBox = document.getElementById('resultado');


form.addEventListener('submit', async (event) => {

    event.preventDefault();

    limpiarResultado();

    const tomo = tomoInput.value.trim();
    const folio = folioInput.value.trim();
    if (!/^[0-9]{1,3}$/.test(tomo) || Number(tomo)<1 || !/^[0-9]{1,4}$/.test(folio) || Number(folio)<1) {
        mostrarError('Ingresá un tomo y un folio válidos, solo con números.');return;
    }
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
                    tomo, folio
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
                        Estamos consultando tu matrícula…
                    </span>

                </div>

            </div>
        `;

        statusBox.classList.add('visible');

    } else {

        submitButton.textContent =
            'Consultar padrón';

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
        <div class="result-header"><div><h2>Figurás en el padrón provisorio</h2></div></div>
        <div class="result-data"><div class="result-grid">${data.fields.map(field => `<div class="result-item"><span>${escapeHTML(field.label)}</span><strong>${escapeHTML(field.value)}</strong></div>`).join('')}</div></div>
        <div class="result-actions"><button type="button" class="pdf-button" id="download-pdf">Descargar consulta (PDF)</button><button type="button" class="new-search" id="new-search">Hacer otra consulta</button></div>`;
    resultadoBox.hidden = false;


    document
        .getElementById('new-search')
        .addEventListener('click', () => {

            form.reset();

            limpiarResultado();

            form.hidden = false;

            tomoInput.focus();

        });

        document
            .getElementById('download-pdf')
            .addEventListener('click', async () => {
                const button = document.getElementById('download-pdf');
                button.disabled = true; button.textContent = 'Preparando PDF…';
                try { await descargarConstancia({
                    tomo: tomoInput.value.trim(), folio: folioInput.value.trim(), fields: data.fields
                }); } catch (e) { mostrarError('No pudimos generar el PDF. Intentá nuevamente.'); }
                finally { button.disabled = false; button.textContent = 'Descargar consulta (PDF)'; }
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
        doc.text(continued ? 'Datos de la consulta' : 'Consulta del padrón provisorio', 20, 75);
        doc.setFont('helvetica', 'normal'); doc.setFontSize(10); doc.setTextColor(...gray);
        doc.text('Consejo de la Magistratura de la Ciudad de Buenos Aires', 20, 85);
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
    field('Matrícula consultada', `Tomo ${datos.tomo} · Folio ${datos.folio}`, false, 12);
    for (const item of datos.fields) field(item.label, item.value);
    const pages = doc.getNumberOfPages();
    for (let page = 1; page <= pages; page++) {
        doc.setPage(page); doc.setDrawColor(...rule); doc.setLineWidth(.3); doc.line(20, 266, 190, 266);
        doc.setFont('helvetica', 'normal'); doc.setFontSize(8); doc.setTextColor(...gray);
        doc.text('Documento informativo, sin validez oficial.', 20, 273);
        doc.text('Fuente de los datos: CPACF · Padrón provisorio.', 20, 278);
        doc.setTextColor(...burgundy); doc.textWithLink('infojuridica.com.ar', 20, 286, { url: 'https://infojuridica.com.ar' });
        doc.setTextColor(...gray); doc.text(`${page} / ${pages}`, 190, 286, { align: 'right' });
    }
    doc.setProperties({ title: 'Consulta del padrón provisorio - INFO JURÍDICA', subject: 'Consulta informativa del padrón electoral', author: 'INFO JURÍDICA' });
    doc.save('infojuridica-padron-provisorio.pdf');
}
