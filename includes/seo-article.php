<?php
require_once __DIR__.'/../app/seo.php';
if(!ij_live() || !empty($isPreview) || !empty($isUnlisted) || ($noticia['status'] ?? '')!=='published')return;
?>
<script type="application/ld+json"><?= ij_seo_json(ij_seo_article($noticia)) ?></script>
