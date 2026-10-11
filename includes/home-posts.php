<?php
$homeNews=ij_posts('news');
require_once __DIR__.'/../app/home-slots.php';
[$lead,$side]=ij_home_selection($homeNews);
?>
<?php if ($lead || $side): ?>
<section class="container"><div class="lead-layout <?= !$lead?'lead-layout-without-main':(!$side?'lead-layout-without-side':'') ?>"><?php if ($lead): ?><article class="lead-story"><a href="<?= ij_e($lead['url']) ?>"><div class="lead-image"><img src="<?= ij_e($lead['imagen']) ?>" alt="<?= ij_e($lead['image_alt']) ?>"></div><span class="category"><?= ij_e($lead['categoria']) ?></span><h1><?= ij_e($lead['titulo']) ?></h1><p class="summary"><?= ij_e($lead['bajada']) ?></p><span class="meta"><?= ij_date($lead['fecha']) ?></span></a></article><?php endif; ?><?php if ($side): ?><aside class="side-news"><?php foreach ($side as $item): ?><article class="side-card"><a href="<?= ij_e($item['url']) ?>"><span class="category"><?= ij_e($item['categoria']) ?></span><h2><?= ij_e($item['titulo']) ?></h2><span class="meta"><?= ij_date($item['fecha']) ?></span></a></article><?php endforeach; ?></aside><?php endif; ?></div></section>
<?php endif; ?>
<?php if ($homeNews): ?>
<section class="section" id="ultimas-noticias"><div class="container"><div class="section-header"><div><div class="section-kicker">Actualidad</div><h2 class="section-title">Últimas noticias</h2></div></div><div class="news-grid"><?php foreach (array_slice($homeNews,0,6) as $item): require __DIR__.'/post-card.php'; endforeach; ?></div><div class="home-section-more"><a href="/noticias/">Todas las noticias</a></div></div></section>
<?php else: ?><section class="container section"><h1 class="section-title">INFO JURÍDICA</h1><p>Próximamente, información para la comunidad jurídica argentina.</p></section><?php endif; ?>
