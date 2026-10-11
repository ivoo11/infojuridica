<?php
require __DIR__.'/app/subscriptions.php';
header('Cache-Control: no-store, private');header('X-Robots-Tag: noindex, nofollow');header('Referrer-Policy: no-referrer');header('X-Frame-Options: DENY');
if (!ij_live()) { http_response_code(503);exit('El servicio no está disponible.'); }
$token=is_string($_GET['token'] ?? null) ? $_GET['token'] : '';
$subscription=ij_subscription_token($token);$done=false;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $done=ij_subscription_unsubscribe($token);
    if (!$done) http_response_code(400);
    if (($_POST['List-Unsubscribe'] ?? '')==='One-Click') { header('Content-Type: text/plain; charset=utf-8');echo $done ? 'Baja registrada.' : 'Enlace inválido.';exit; }
} elseif ($_SERVER['REQUEST_METHOD']!=='GET') { http_response_code(405);header('Allow: GET, POST');exit; }
if (!$subscription) http_response_code(400);
$noticia=['titulo'=>'Baja del newsletter','bajada'=>'Gestionar la suscripción a INFO JURÍDICA.'];$isPreview=true;$hideNewsletterSignup=true;
require __DIR__.'/includes/listing-head.php';require __DIR__.'/includes/header.php';
?>
<link rel="stylesheet" href="/assets/css/newsletter-signup.css?v=<?= filemtime(__DIR__.'/assets/css/newsletter-signup.css') ?>">
<main class="container newsletter-registration"><h1>Baja del newsletter</h1>
<?php if ($done || ($subscription && $subscription['status']==='unsubscribed')): ?><p class="newsletter-feedback" role="status">Tu baja quedó registrada. No recibirás más newsletters.</p>
<?php elseif (!$subscription): ?><p class="newsletter-feedback">Este enlace no es válido o fue reemplazado. Usá el enlace del correo más reciente.</p>
<?php else: ?><p>Podés dejar de recibir el newsletter sin iniciar sesión.</p><form method="post" action="/newsletter-baja.php?token=<?= ij_e($token) ?>" class="newsletter-registration-box"><button class="newsletter-signup-button">Confirmar baja</button></form><?php endif; ?>
<p><a href="/">Volver al portal</a></p></main><?php require __DIR__.'/includes/footer.php'; ?>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__.'/assets/js/app.js') ?>"></script><script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__.'/assets/js/navigation.js') ?>"></script></body></html>
