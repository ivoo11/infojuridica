<?php
$context=array_values(array_filter($homeNews ?? ij_posts('news'),fn($p)=>$p['in_context']));
usort($context,fn($a,$b)=>($a['home_order']<=>$b['home_order']) ?: strcmp($b['fecha'],$a['fecha']));
if (!$context) return;
?>
<section class="section" id="en-contexto"><div class="container"><div class="section-header"><div><div class="section-kicker">Explicadores</div><h2 class="section-title">En contexto</h2></div></div><div class="context-grid"><?php foreach (array_slice($context,0,3) as $i=>$item): ?><article class="context-card <?= $i===0?'main':'' ?>"><span class="context-number"><?= sprintf('%02d',$i+1) ?></span><span class="category"><?= ij_e($item['categoria']) ?></span><h3><a href="<?= ij_e($item['url']) ?>"><?= ij_e($item['titulo']) ?></a></h3><p class="summary"><?= ij_e($item['bajada']) ?></p></article><?php endforeach; ?></div></div></section>
