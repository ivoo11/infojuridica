<?php require_once __DIR__ . '/app/content.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Info Jurídica — Información para la comunidad jurídica argentina</title>

    <meta
        name="description"
        content="Actualidad, justicia, elecciones, profesión y herramientas para la comunidad jurídica argentina."
    >

    <?php require __DIR__ . '/includes/favicon.php'; ?>

    <!-- TIPOGRAFÍAS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600;8..60,700&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet"
      href="/assets/css/styles.css?v=<?= filemtime(__DIR__ . '/assets/css/styles.css') ?>">

<link rel="stylesheet" href="/assets/css/navigation.css?v=<?= filemtime(__DIR__ . '/assets/css/navigation.css') ?>">
<link rel="stylesheet" href="/assets/css/home.css?v=<?= filemtime(__DIR__ . '/assets/css/home.css') ?>">
</head>

<body>

<?php require __DIR__ . '/includes/header.php'; ?>
<?php require __DIR__ . '/includes/navbar.php'; ?>
    <?php require __DIR__ . '/includes/breaking-home.php'; ?>

    <main>

        <?php if (ij_live()): require __DIR__ . '/includes/home-posts.php'; else: ?>
        <!-- PORTADA -->
        <section class="container">

            <div class="lead-layout">

                <article class="lead-story">
                    <a href="/noticia.php?id=0">
                        <div class="lead-image">
                            <img
                                src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=1600&q=85"
                                alt=""
                            >
                        </div>

                        <span class="category">JUSTICIA</span>

                        <h1>
                            La agenda jurídica entra en una etapa decisiva hacia el cierre del año
                        </h1>

                        <p class="summary">
                            Elecciones profesionales, nuevos debates institucionales y decisiones
                            judiciales marcan las próximas semanas de la comunidad jurídica argentina.
                        </p>

                        <span class="meta">
                            22 SEP 2026 · 6 MIN
                        </span>
                    </a>
                </article>

                <aside class="side-news">

                    <article class="side-card">
                        <a href="/noticia.php?id=1">
                            <span class="category">COLEGIATURA</span>
                            <h2>
                                Consejo de la Magistratura: qué se elige y quiénes pueden votar
                            </h2>
                            <span class="meta">22 SEP 2026 · 4 MIN</span>
                        </a>
                    </article>

                    <article class="side-card">
                        <a href="/noticia.php?id=2">
                            <span class="category">PROFESIÓN</span>
                            <h2>
                                Las nuevas tecnologías empiezan a cambiar la práctica jurídica
                            </h2>
                            <span class="meta">21 SEP 2026 · 5 MIN</span>
                        </a>
                    </article>

                    <article class="side-card">
                        <a href="/noticia.php?id=3">
                            <span class="category">COLEGIATURA</span>
                            <h2>
                                Los colegios de abogados preparan sus próximas agendas electorales
                            </h2>
                            <span class="meta">21 SEP 2026 · 3 MIN</span>
                        </a>
                    </article>

                </aside>

            </div>

        </section>


        <!-- ÚLTIMAS NOTICIAS -->
        <section class="section" id="ultimas-noticias">
            <div class="container">

                <div class="section-header">
                    <div>
                        <div class="section-kicker">Actualidad</div>
                        <h2 class="section-title">Últimas noticias</h2>
                    </div>
                </div>


                <div class="news-grid">

                    <article class="news-card">
                        <a href="/noticia.php?id=4">
                            <div class="news-card-image">
                                <img
                                    src="https://images.unsplash.com/photo-1436450412740-6b988f486c6b?auto=format&fit=crop&w=900&q=80"
                                    alt=""
                                >
                            </div>

                            <span class="category">COLEGIATURA</span>

                            <h3>
                                Se oficializó el cronograma para las próximas elecciones de la abogacía
                            </h3>

                            <p class="summary">
                                Fechas, requisitos y principales instancias del proceso electoral.
                            </p>

                            <span class="meta">22 SEP 2026 · 4 MIN</span>
                        </a>
                    </article>


                    <article class="news-card">
                        <a href="/noticia.php?id=5">
                            <div class="news-card-image">
                                <img
                                    src="https://images.unsplash.com/photo-1453945619913-79ec89a82c51?auto=format&fit=crop&w=900&q=80"
                                    alt=""
                                >
                            </div>

                            <span class="category">JUSTICIA</span>

                            <h3>
                                Un nuevo fallo vuelve a poner el foco sobre los límites de la administración
                            </h3>

                            <p class="summary">
                                La decisión analiza una discusión recurrente del derecho público.
                            </p>

                            <span class="meta">21 SEP 2026 · 7 MIN</span>
                        </a>
                    </article>


                    <article class="news-card">
                        <a href="/noticia.php?id=6">
                            <div class="news-card-image">
                                <img
                                    src="https://images.unsplash.com/photo-1505664194779-8beaceb93744?auto=format&fit=crop&w=900&q=80"
                                    alt=""
                                >
                            </div>

                            <span class="category">PROFESIÓN</span>

                            <h3>
                                La formación jurídica empieza a incorporar nuevas competencias digitales
                            </h3>

                            <p class="summary">
                                Universidades e instituciones revisan sus propuestas de capacitación.
                            </p>

                            <span class="meta">21 SEP 2026 · 5 MIN</span>
                        </a>
                    </article>


                    <article class="news-card">
                        <a href="/noticia.php?id=7">
                            <div class="news-card-image">
                                <img
                                    src="https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=900&q=80"
                                    alt=""
                                >
                            </div>

                            <span class="category">UNIVERSIDADES</span>

                            <h3>
                                Las facultades de Derecho amplían su agenda académica para el último trimestre
                            </h3>

                            <p class="summary">
                                Jornadas, congresos y programas ejecutivos ya tienen fecha.
                            </p>

                            <span class="meta">20 SEP 2026 · 3 MIN</span>
                        </a>
                    </article>


                    <article class="news-card">
                        <a href="/noticia.php?id=8">
                            <div class="news-card-image">
                                <img
                                    src="https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=80"
                                    alt=""
                                >
                            </div>

                            <span class="category">JUSTICIA</span>

                            <h3>
                                Las principales novedades normativas que dejó la última semana
                            </h3>

                            <p class="summary">
                                Una selección de disposiciones y normas relevantes para la práctica profesional.
                            </p>

                            <span class="meta">19 SEP 2026 · 5 MIN</span>
                        </a>
                    </article>


                    <article class="news-card">
                        <a href="/noticia.php?id=9">
                            <div class="news-card-image">
                                <img
                                    src="https://images.unsplash.com/photo-1453873531674-2151bcd01707?auto=format&fit=crop&w=900&q=80"
                                    alt=""
                                >
                            </div>

                            <span class="category">PROFESIÓN</span>

                            <h3>
                                Inteligencia artificial y Derecho: dónde termina la asistencia y empieza el riesgo
                            </h3>

                            <p class="summary">
                                El debate sobre IA generativa ya atraviesa estudios, empresas y tribunales.
                            </p>

                            <span class="meta">19 SEP 2026 · 8 MIN</span>
                        </a>
                    </article>

                </div>

            </div>
        </section>


        <?php endif; ?>
        <?php require __DIR__ . '/includes/publicidad-home.php'; ?>
        <?php require __DIR__ . '/includes/opinion-home.php'; ?>

        <?php if (ij_live()): require __DIR__ . '/includes/context-posts.php'; else: ?>
        <!-- EN CONTEXTO -->
        <section class="section" id="en-contexto">
            <div class="container">

                <div class="section-header">
                    <div>
                        <div class="section-kicker">Explicadores</div>
                        <h2 class="section-title">En contexto</h2>
                    </div>
                </div>


                <div class="context-grid">

                    <article class="context-card main">
                        <span class="context-number">01</span>

                        <span class="category">GUÍA</span>

                        <h3><a href="/noticia.php?id=10">
                            ¿Qué es el Consejo de la Magistratura y cómo funciona?
                        </a></h3>

                        <p class="summary">
                            Una guía para entender su composición, atribuciones y sistema de representación.
                        </p>
                    </article>


                    <article class="context-card">
                        <span class="context-number">02</span>

                        <span class="category">EN 5 MINUTOS</span>

                        <h3><a href="/noticia.php?id=11">
                            Cómo se designa un juez en Argentina
                        </a></h3>

                        <p class="summary">
                            Las etapas que atraviesa una candidatura antes de llegar a un tribunal.
                        </p>
                    </article>


                    <article class="context-card">
                        <span class="context-number">03</span>

                        <span class="category">PROFESIÓN</span>

                        <h3><a href="/noticia.php?id=12">
                            Colegio, matrícula y federación: qué función cumple cada institución
                        </a></h3>

                        <p class="summary">
                            Una explicación simple sobre estructuras que conviven dentro de la profesión.
                        </p>
                    </article>

                </div>

            </div>
        </section>


        <?php endif; ?>
        <?php require __DIR__ . '/includes/agenda-home.php'; ?>

    </main>


<?php require __DIR__ . '/includes/footer.php'; ?>
    <script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/assets/js/app.js') ?>"></script>

<script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__ . '/assets/js/navigation.js') ?>"></script>
<script src="/assets/js/home.js?v=<?= filemtime(__DIR__ . '/assets/js/home.js') ?>"></script>
</body>
</html>
