<?php
require_once __DIR__.'/../app/content.php';
$columns=ij_posts('opinion');$pages=max(1,(int)ceil(count($columns)/6));
$requested=filter_var($_GET['pagina'] ?? 1,FILTER_VALIDATE_INT);$page=min($pages,max(1,$requested ?: 1));
$items=array_slice($columns,($page-1)*6,6);
$noticia=['titulo'=>'Opinión','bajada'=>'Todas las columnas de INFO JURÍDICA.'];
require __DIR__.'/../includes/listing-head.php';require __DIR__.'/../includes/header.php';
?>
<link rel="stylesheet" href="/assets/css/opinion-archive.css?v=<?= filemtime(__DIR__.'/../assets/css/opinion-archive.css') ?>">
<main class="container listing-page opinion-archive">
<header class="listing-heading listing-heading-category"><h1>Opinión</h1></header>
<?php if(!$items):?><p>Todavía no hay columnas publicadas.</p><?php else:?>
<div class="opinion-archive-grid"><?php foreach($items as $column):?>
<article class="opinion-archive-card"><a href="<?= ij_e($column['url']) ?>"><div class="opinion-archive-author"><img src="<?= ij_e(($column['author_photo'] ?? '') ?: '/assets/img/autor-placeholder.svg') ?>" alt="<?= ij_e('Retrato de '.($column['author_name'] ?? 'autor')) ?>" width="72" height="72" loading="lazy"><div><strong><?= ij_e($column['author_name'] ?? 'Autor de demostración') ?></strong><?php if(!empty($column['author_role'])):?><span><?= ij_e($column['author_role']) ?></span><?php endif;?></div></div><h2><?= ij_e($column['titulo']) ?></h2><p><?= ij_e($column['bajada']) ?></p><time datetime="<?= ij_e($column['fecha']) ?>"><?= ij_date($column['fecha']) ?></time></a></article>
<?php endforeach;?></div>
<?php endif;?>
<?php if($pages>1):?><nav class="listing-pagination" aria-label="Páginas de opinión"><?php for($i=1;$i<=$pages;$i++):?><?php if($i===$page):?><span aria-current="page"><?= $i ?></span><?php else:?><a href="/opinion/?pagina=<?= $i ?>"><?= $i ?></a><?php endif;?><?php endfor;?></nav><?php endif;?>
</main>
<?php require __DIR__.'/../includes/footer.php'; ?>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__.'/../assets/js/app.js') ?>"></script><script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__.'/../assets/js/navigation.js') ?>"></script>
</body></html>
