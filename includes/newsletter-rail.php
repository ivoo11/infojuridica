<?php if (function_exists('ij_live') && ij_live()): $hideNewsletterSignup=true; ?>
<link rel="stylesheet" href="/assets/css/newsletter-rail.css?v=<?= filemtime(__DIR__.'/../assets/css/newsletter-rail.css') ?>">
<section class="newsletter-rail" aria-labelledby="railNewsletterTitle">
    <div class="newsletter-rail-masthead"><span>INFO JURÍDICA</span><span>Newsletter</span></div>
    <h2 id="railNewsletterTitle">La actualidad,<br><em>en tu correo.</em></h2>
    <p class="newsletter-rail-description">Noticias, análisis y próximas actividades para seguir la actualidad jurídica.</p>
    <form action="/newsletter.php" method="post" data-newsletter-signup>
        <input type="hidden" name="consent_version" value="2026-10-11-button">
        <div hidden aria-hidden="true"><label>No completar<input name="website" tabindex="-1" autocomplete="off"></label></div>
        <label class="newsletter-rail-label" for="railNewsletterEmail">Tu correo electrónico</label>
        <input id="railNewsletterEmail" class="newsletter-rail-email" name="email" type="email" required maxlength="190" autocomplete="email" placeholder="Tu correo electrónico" aria-describedby="railNewsletterConsent">
        <button type="submit" name="consent" value="1">Suscribirme</button>
        <p id="railNewsletterConsent" class="newsletter-rail-consent">Al hacer clic en Suscribirme, aceptás recibir el newsletter de INFO JURÍDICA. Podés darte de baja cuando quieras.</p>
        <p class="newsletter-rail-status" role="status" aria-live="polite" hidden></p>
        <noscript><p><a href="/newsletter.php">Abrir el formulario de suscripción</a></p></noscript>
    </form>
</section>
<script defer src="/assets/js/newsletter-signup.js?v=<?= filemtime(__DIR__.'/../assets/js/newsletter-signup.js') ?>"></script>
<?php endif; ?>
