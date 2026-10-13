<?php
require_once __DIR__.'/app/content.php';
$opinionId=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);
$noticia=$opinionId===false ? null : ij_post($opinionId,'opinion');
if (!$noticia) { $errorKind='publication';require __DIR__.'/404.php';return; }
$isOpinion=true;require __DIR__.'/noticia.php';
