<?php
$headerNow = new DateTimeImmutable('now', new DateTimeZone('America/Argentina/Buenos_Aires'));
$headerDays = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$headerMonths = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
$headerDate = $headerDays[(int) $headerNow->format('w')] . ' ' . $headerNow->format('j') . ' de ' . $headerMonths[(int) $headerNow->format('n') - 1] . ' de ' . $headerNow->format('Y');
?>
    <!-- TOPBAR -->
    <div class="topbar">
        <div class="container topbar-inner">
            <div class="topbar-date">Argentina · <time id="argentinaClock" datetime="<?= $headerNow->format('Y-m-d') ?>" data-timestamp="<?= $headerNow->format('Uv') ?>"><?= $headerDate ?></time></div>
        </div>
    </div>
    <script defer src="/assets/js/clock.js?v=<?= filemtime(__DIR__ . '/../assets/js/clock.js') ?>"></script>

    <!-- LOGO -->
<header class="masthead">
    <div class="container masthead-inner">

        <a class="brand" href="/" aria-label="Info Jurídica - Inicio">
            <img
                src="/assets/img/logo-infojuridica.png"
                alt="Info Jurídica"
                class="brand-logo"
            >
        </a>

        <button class="mobile-menu" type="button" aria-controls="mainNav" aria-expanded="false" aria-label="Abrir secciones">
            <span class="menu-icon" aria-hidden="true"><span></span><span></span><span></span></span>
        </button>

        <?php require __DIR__ . '/navbar.php'; ?>
    </div>
</header>
