<?php
// Social previews use public absolute URLs; private previews publish no metadata.
if (!ij_live() || !empty($isPreview)) return;
$origin='https://infojuridica.com.ar';
$socialUrl=$origin.$noticia['url'];
$socialImage=($noticia['image'] ?? '') ?: (!empty($isOpinion) ? ($noticia['author_photo'] ?? '') : ($noticia['imagen'] ?? ''));
if (!$socialImage || str_ends_with($socialImage,'.svg')) $socialImage='/assets/img/logo-infojuridica.png';
$socialImageUrl=str_starts_with($socialImage,'/') ? $origin.$socialImage : $socialImage;
$socialSize=false;
if (preg_match('~^/(?:uploads|assets/img)/[a-zA-Z0-9._-]+$~D',$socialImage)) $socialSize=@getimagesize(__DIR__.'/..'.$socialImage);
?>
<meta property="og:type" content="article">
<meta property="og:site_name" content="INFO JURÍDICA">
<meta property="og:locale" content="es_AR">
<meta property="og:title" content="<?= ij_e($noticia['titulo']) ?>">
<meta property="og:description" content="<?= ij_e($noticia['bajada']) ?>">
<meta property="og:url" content="<?= ij_e($socialUrl) ?>">
<meta property="og:image" content="<?= ij_e($socialImageUrl) ?>">
<meta property="og:image:alt" content="<?= ij_e(($noticia['image_alt'] ?? '') ?: $noticia['titulo']) ?>">
<?php if ($socialSize): ?>
<meta property="og:image:width" content="<?= (int)$socialSize[0] ?>">
<meta property="og:image:height" content="<?= (int)$socialSize[1] ?>">
<meta property="og:image:type" content="<?= ij_e($socialSize['mime']) ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= ij_e($noticia['titulo']) ?>">
<meta name="twitter:description" content="<?= ij_e($noticia['bajada']) ?>">
<meta name="twitter:image" content="<?= ij_e($socialImageUrl) ?>">
<meta name="twitter:image:alt" content="<?= ij_e(($noticia['image_alt'] ?? '') ?: $noticia['titulo']) ?>">
