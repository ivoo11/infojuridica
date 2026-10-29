<?php
declare(strict_types=1);
require_once __DIR__.'/../app/bootstrap.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Padrón provisorio | Consejo de la Magistratura de la Ciudad</title>

    <meta
        name="description"
        content="Consulta individual del padrón provisorio del CPACF por tomo y folio."
    >

    <?php require __DIR__ . '/../includes/favicon.php'; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600;8..60,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/styles.css?v=<?= filemtime(__DIR__.'/../assets/css/styles.css') ?>">
    <link rel="stylesheet" href="/assets/css/navigation.css?v=<?= filemtime(__DIR__.'/../assets/css/navigation.css') ?>">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= filemtime(__DIR__.'/assets/css/styles.css') ?>">
</head>

<body class="padron-page">
<?php require __DIR__.'/../includes/header.php'; ?>

<main class="container padron-tool">


    <section class="hero">

        <div class="hero-copy">

            <h1>
                Consultá el padrón provisorio
            </h1>

            <p class="intro">
                Elecciones del Consejo de la Magistratura de la Ciudad de Buenos Aires.
            </p>

        </div>


        <div class="search-card">

            <form id="padron-form">
                <p class="form-intro">Ingresá el tomo y el folio de tu matrícula del CPACF.</p>
                <div class="matricula-fields">

                <div class="field"><label for="tomo">Tomo</label><input type="text" id="tomo" name="tomo" inputmode="numeric" autocomplete="off" maxlength="3" pattern="[0-9]{1,3}" placeholder="Ej. 123" required></div>
                <div class="field"><label for="folio">Folio</label><input type="text" id="folio" name="folio" inputmode="numeric" autocomplete="off" maxlength="4" pattern="[0-9]{1,4}" placeholder="Ej. 4567" required></div>
                </div>
                <button
                    type="submit"
                    class="submit-button"
                    id="submit-button"
                >
                    Consultar padrón
                </button>

            </form>


            <div
                id="status"
                class="status"
                aria-live="polite"
            ></div>


            <div
                id="resultado"
                class="resultado"
                hidden
            ></div>

            <aside class="padron-notice" aria-label="Información sobre la consulta"><p>Consulta informativa con datos del CPACF. INFO JURÍDICA no guarda el tomo ni el folio ingresados.</p></aside>
        </div>

    </section>




</main>
<?php require __DIR__.'/../includes/footer.php'; ?>

<script src="assets/js/jspdf.umd.min.js?v=<?= filemtime(__DIR__.'/assets/js/jspdf.umd.min.js') ?>"></script>
<script src="assets/js/app.js?v=<?= filemtime(__DIR__.'/assets/js/app.js') ?>"></script>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__.'/../assets/js/app.js') ?>"></script>
<script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__.'/../assets/js/navigation.js') ?>"></script>

</body>
</html>