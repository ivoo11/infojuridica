<?php
require __DIR__.'/../app/admin.php';ij_require_user();require __DIR__.'/../app/content.php';
$id=filter_var($_GET['id'] ?? null,FILTER_VALIDATE_INT);$noticia=null;
if ($id!==false && $id!==null) { $s=ij_db()->prepare('SELECT type FROM posts WHERE id=?');$s->execute([$id]);$type=$s->fetchColumn();if ($type) $noticia=ij_post($id,$type,true); }
if (!$noticia) { http_response_code(404);exit('Publicación no encontrada.'); }
$isOpinion=$noticia['type']==='opinion';$isPreview=true;
require __DIR__.'/../noticia.php';
