<?php
require __DIR__.'/app/bootstrap.php';
require __DIR__.'/app/subscriptions.php';
header('Cache-Control: no-store, private');header('X-Robots-Tag: noindex, nofollow');header('X-Content-Type-Options: nosniff');
if (!ij_live()) { http_response_code(503);exit('La suscripción todavía no está habilitada.'); }
ini_set('session.use_strict_mode','1');ini_set('session.use_only_cookies','1');
session_name('ij_newsletter');session_set_cookie_params(['lifetime'=>0,'path'=>'/newsletter.php','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','httponly'=>true,'samesite'=>'Lax']);session_start();
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
$error='';$email='';$accepted=false;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'],$_POST['csrf'])) { http_response_code(403);exit('Volvé a cargar el formulario para continuar.'); }
    try {
        $email=ij_subscription_email($_POST);$accepted=($_POST['consent'] ?? '')==='1';
        if (!empty($_POST['website'])) throw new InvalidArgumentException('No pudimos registrar la solicitud.');
        ij_subscription_request($email,$_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $_SESSION['signup_success']=true;$_SESSION['csrf']=bin2hex(random_bytes(32));
        header('Location: /newsletter.php?registrado=1',true,303);exit;
    } catch (InvalidArgumentException $e) { $error=$e->getMessage(); $email=is_string($_POST['email'] ?? null) ? substr($_POST['email'],0,190) : ''; }
}
$success=isset($_GET['registrado']) && !empty($_SESSION['signup_success']);unset($_SESSION['signup_success']);
$noticia=['titulo'=>'Suscribirse al newsletter','bajada'=>'Noticias y próximas actividades en tu correo.'];$isPreview=true;$hideNewsletterSignup=true;require __DIR__.'/includes/listing-head.php';require __DIR__.'/includes/header.php';
?>
<link rel="stylesheet" href="/assets/css/newsletter-signup.css?v=<?= filemtime(__DIR__.'/assets/css/newsletter-signup.css') ?>"><main class="container newsletter-registration"><h1>Suscribite al newsletter</h1>
<p>Noticias y próximas actividades de INFO JURÍDICA, en tu correo.</p>
<?php if ($success): ?><p class="newsletter-feedback" role="status">Registramos tu solicitud. Cuando habilitemos los envíos, te enviaremos un correo para confirmar la suscripción.</p><a class="newsletter-signup-button" href="/">Volver al portal</a>
<?php else: ?>
<?php if ($error): ?><p class="newsletter-feedback" role="alert"><?= ij_e($error) ?></p><?php endif; ?>
<form class="newsletter-registration-box" method="post"><input type="hidden" name="csrf" value="<?= ij_e($_SESSION['csrf']) ?>">
<label>Tu correo<input type="email" name="email" required maxlength="190" autocomplete="email" value="<?= ij_e($email) ?>"></label>
<div hidden aria-hidden="true"><label>No completar<input name="website" tabindex="-1" autocomplete="off"></label></div>
<label class="newsletter-consent"><input type="checkbox" name="consent" value="1" required <?= $accepted ? 'checked' : '' ?>><span><?= ij_e(ij_subscription_consent()) ?></span></label>
<p class="newsletter-registration-help">Usaremos tu correo para el newsletter. Registramos la fecha y el texto de tu aceptación. La suscripción quedará pendiente hasta que confirmes tu correo; el servicio de envío está en preparación.</p>
<button class="newsletter-signup-button">Solicitar suscripción</button><p><a href="/">Volver al portal</a></p></form>
<?php endif; ?></main><?php require __DIR__.'/includes/footer.php'; ?>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__.'/assets/js/app.js') ?>"></script><script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__.'/assets/js/navigation.js') ?>"></script></body></html>
