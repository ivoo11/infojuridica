<?php if (empty($hideNewsletterSignup) && function_exists('ij_live') && ij_live()): ?>
<link rel="stylesheet" href="/assets/css/newsletter-signup.css?v=<?= filemtime(__DIR__.'/../assets/css/newsletter-signup.css') ?>">
<section class="newsletter-signup" aria-labelledby="newsletterSignupTitle"><div class="container newsletter-signup-inner"><div><span class="newsletter-signup-kicker">Newsletter</span><h2 id="newsletterSignupTitle">INFO JURÍDICA en tu correo</h2><p>Las noticias del portal y las próximas actividades, en una edición para leer con calma.</p></div><div><a class="newsletter-signup-button" href="/newsletter.php">Quiero suscribirme</a><p class="newsletter-signup-note">Elegís sumarte. Podés darte de baja cuando quieras.</p></div></div></section>
<?php endif; ?>
