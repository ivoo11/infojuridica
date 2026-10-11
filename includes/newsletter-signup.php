<?php if (empty($hideNewsletterSignup) && function_exists('ij_live') && ij_live()): ?>
<link rel="stylesheet" href="/assets/css/newsletter-signup.css?v=<?= filemtime(__DIR__.'/../assets/css/newsletter-signup.css') ?>">
<section class="newsletter-signup" aria-labelledby="newsletterSignupTitle">
<div class="container newsletter-signup-inner">
    <div class="newsletter-signup-copy"><span class="newsletter-signup-kicker">Newsletter</span><h2 id="newsletterSignupTitle">Recibí las últimas noticias</h2><p>Actualidad jurídica y próximas actividades, en tu correo.</p></div>
    <form class="newsletter-inline-form" action="/newsletter.php" method="post" data-newsletter-signup>
        <input type="hidden" name="consent_version" value="2026-10-11-button">
        <div hidden aria-hidden="true"><label>No completar<input name="website" tabindex="-1" autocomplete="off"></label></div>
        <label for="newsletterEmail" class="newsletter-email-label">Tu correo electrónico</label>
        <div class="newsletter-input-row"><input id="newsletterEmail" name="email" type="email" required maxlength="190" autocomplete="email" placeholder="Tu correo electrónico" aria-describedby="newsletterConsent"><button type="submit" name="consent" value="1">Suscribirme</button></div>
        <p id="newsletterConsent" class="newsletter-signup-note">Al hacer clic en Suscribirme, aceptás recibir el newsletter de INFO JURÍDICA. Podés darte de baja cuando quieras.</p>
        <p class="newsletter-signup-status" role="status" aria-live="polite" hidden></p>
        <noscript><p><a href="/newsletter.php">Abrir el formulario de suscripción</a></p></noscript>
    </form>
</div></section>
<script defer src="/assets/js/newsletter-signup.js?v=<?= filemtime(__DIR__.'/../assets/js/newsletter-signup.js') ?>"></script>
<?php endif; ?>
