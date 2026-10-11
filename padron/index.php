<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Consulta de padrón electoral | Elecciones Consejo de la Magistratura</title>

    <meta
        name="description"
        content="Consulta individual de lugar de votación."
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

            <p class="eyebrow">
                Elecciones 2026 · Consejo de la Magistratura de la Nación
            </p>

            <h1>
                Consultá tu lugar de votación
            </h1>

            <p class="intro">
                Consulta del padrón de la abogacía para las elecciones del Consejo de la Magistratura de la Nación. Ingresá tu DNI y el sexo registrado en el padrón.
            </p>

        </div>


        <div class="search-card">

            <form id="padron-form">

                <div class="field">

                    <label for="dni">
                        DNI
                    </label>

                    <input
                        type="text"
                        id="dni"
                        name="dni"
                        inputmode="numeric"
                        autocomplete="off"
                        maxlength="8"
                        placeholder="Sin puntos"
                        required
                    >

                </div>


                <fieldset class="field">

                    <legend>
                        Sexo
                    </legend>

                    <div class="sex-options">

                        <label class="radio-option">
                            <input
                                type="radio"
                                name="sexo"
                                value="F"
                                required
                            >
                            <span>Femenino</span>
                        </label>

                        <label class="radio-option">
                            <input
                                type="radio"
                                name="sexo"
                                value="M"
                            >
                            <span>Masculino</span>
                        </label>

                    </div>

                </fieldset>


                <button
                    type="submit"
                    class="submit-button"
                    id="submit-button"
                >
                    Consultar lugar de votación
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

        </div>

    </section>


<aside class="padron-notice" aria-label="Información sobre la consulta">

    <p>
        Herramienta independiente de consulta electoral.
        No constituye un sitio oficial del Consejo de la Magistratura de la Nación.
        Los datos ingresados se utilizan exclusivamente para realizar la consulta
        y no se almacenan en este sitio. Su tratamiento se realiza conforme a la
        Ley Nº 25.326 de Protección de los Datos Personales.
        La consulta es procesada mediante una fuente externa.
    </p>

</aside>

</main>
<?php require __DIR__.'/../includes/footer.php'; ?>

<script src="assets/js/jspdf.umd.min.js?v=<?= filemtime(__DIR__.'/assets/js/jspdf.umd.min.js') ?>"></script>
<script src="assets/js/app.js?v=<?= filemtime(__DIR__.'/assets/js/app.js') ?>"></script>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__.'/../assets/js/app.js') ?>"></script>
<script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__.'/../assets/js/navigation.js') ?>"></script>

</body>
</html>