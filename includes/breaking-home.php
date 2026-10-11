<?php
require_once __DIR__.'/../app/settings.php';
$breakingItem=ij_ahora();
if (trim($breakingItem['texto'] ?? '')==='') return;
?>
<div class="breaking"><div class="container breaking-inner"><div class="breaking-label">AHORA</div>
<?php if (!empty($breakingItem['url'])): ?><a class="breaking-news" target="_blank" rel="noopener noreferrer" href="<?= ij_e($breakingItem['url']) ?>"><?= ij_e($breakingItem['texto']) ?></a><?php else: ?><span class="breaking-news"><?= ij_e($breakingItem['texto']) ?></span><?php endif; ?>
</div></div>
