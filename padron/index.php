<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Consulta de padrón electoral | ELecciones Consejo de la Magistratura</title>

    <meta
        name="description"
        content="Consulta individual de lugar de votación."
    >

    <link rel="icon" href="favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>

<main class="site">

    <header class="header">
        <div class="brand">
            <span class="brand-kicker">ELECCIONES</span>

            <span class="brand-title">
                CONSEJO DE LA MAGISTRATURA
            </span>

            <span class="brand-country">
                DE LA NACIÓN
            </span>
        </div>
    </header>


    <section class="hero">

        <div class="hero-copy">

            <p class="eyebrow">
                Consulta electoral
            </p>

            <h1>
                Consultá dónde<br>
                te corresponde votar.
            </h1>

            <p class="intro">
                Ingresá tus datos para consultar tu lugar de votación.
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


<footer class="footer">

    <p>
        Herramienta independiente de consulta electoral.
        No constituye un sitio oficial del Consejo de la Magistratura de la Nación.
        Los datos ingresados se utilizan exclusivamente para realizar la consulta
        y no se almacenan en este sitio. Su tratamiento se realiza conforme a la
        Ley Nº 25.326 de Protección de los Datos Personales.
        La consulta es procesada mediante una fuente externa.
    </p>

</footer>

</main>

<script src="assets/js/app.js"></script>

</body>
</html>