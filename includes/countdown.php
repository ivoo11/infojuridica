<?php
require_once __DIR__.'/../app/countdown.php';
$countdown=ij_countdown();$target=ij_countdown_target($countdown['target']);$now=time();
if($countdown['enabled'] && $target && $target->getTimestamp()>$now):
$remaining=$target->getTimestamp()-$now;
$months=['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
?>
<link rel="stylesheet" href="/assets/css/countdown.css?v=<?= filemtime(__DIR__.'/../assets/css/countdown.css') ?>">
<section class="countdown-section" data-countdown data-target="<?= $target->getTimestamp()*1000 ?>" data-now="<?= $now*1000 ?>" aria-labelledby="countdown-title">
<div class="countdown-art" aria-hidden="true"></div><div class="container countdown-wrap"><div class="countdown-banner"><div class="countdown-content">
<h2 id="countdown-title"><?= ij_e($countdown['title']) ?></h2>
<p class="countdown-date"><time datetime="<?= $target->format('c') ?>"><?= $target->format('j').' de '.$months[(int)$target->format('n')-1].' de '.$target->format('Y') ?></time></p>
<div class="countdown-clock" aria-hidden="true"><?php foreach(['days'=>[intdiv($remaining,86400),'días'],'hours'=>[intdiv($remaining,3600)%24,'horas'],'minutes'=>[intdiv($remaining,60)%60,'minutos'],'seconds'=>[$remaining%60,'segundos']] as $key=>[$value,$label]):?><div class="countdown-time"><span class="countdown-value" data-unit="<?= $key ?>"><?php foreach(str_split(str_pad((string)$value,2,'0',STR_PAD_LEFT)) as $digit):?><span><?= $digit ?></span><?php endforeach;?></span><span class="countdown-unit"><?= $label ?></span></div><?php endforeach;?></div>
<?php if($countdown['url']!==''):?><a class="countdown-action" href="<?= ij_e($countdown['url']) ?>"><?= ij_e($countdown['label']) ?></a><?php endif;?>
</div></div></div></section>
<script defer src="/assets/js/countdown.js?v=<?= filemtime(__DIR__.'/../assets/js/countdown.js') ?>"></script>
<?php endif;?>
