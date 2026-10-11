<?php
require_once __DIR__.'/app/content.php';
$opinionId=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);
$noticia=$opinionId===false ? null : ij_post($opinionId,'opinion');
if (!$noticia) { http_response_code(404);echo 'Columna no encontrada.';return; }
$isOpinion=true;require __DIR__.'/noticia.php';
