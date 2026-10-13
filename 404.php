<?php
require_once __DIR__.'/app/content.php';
http_response_code(404);header('X-Robots-Tag: noindex, nofollow');header('Cache-Control: no-store');
$isPreview=true;$hideNewsletterSignup=true;
$unavailable=($errorKind ?? '')==='publication';
$noticia=['titulo'=>$unavailable?'Esta publicación no está disponible':'No encontramos esta página','bajada'=>'Podés seguir leyendo las últimas noticias de INFO JURÍDICA.'];
$recommendations=array_slice(ij_posts('news'),0,3);
require __DIR__.'/includes/listing-head.php';require __DIR__.'/includes/header.php';
?>
<link rel="stylesheet" href="/assets/css/error-page.css?v=<?= filemtime(__DIR__.'/assets/css/error-page.css') ?>">
<main class="container error-page">
<section class="error-intro"><h1><?= ij_e($noticia['titulo']) ?></h1><p><?= $unavailable?'Puede que el enlace haya cambiado o que la nota ya no esté publicada.':'El enlace puede ser incorrecto o la página ya no estar disponible.' ?></p><div class="error-actions"><a class="error-primary" href="/">Volver a la portada</a><a href="/noticias/">Ver todas las noticias</a></div></section>
<?php if($recommendations):?><section class="error-reading" aria-labelledby="continueReading"><h2 id="continueReading">Seguí leyendo</h2><div class="news-grid"><?php foreach($recommendations as $item):require __DIR__.'/includes/post-card.php';endforeach;?></div></section><?php endif;?>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__.'/assets/js/app.js') ?>"></script><script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__.'/assets/js/navigation.js') ?>"></script>
</body></html>
