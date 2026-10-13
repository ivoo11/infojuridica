<?php
require_once __DIR__.'/app/content.php';
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '',PHP_URL_PATH) ?? '');
if (!preg_match('~^/(noticias|opinion)/([a-z0-9-]+)/?$~D',$path,$match)) { $errorKind='publication';require __DIR__.'/404.php';return; }
$type=$match[1]==='opinion' ? 'opinion' : 'news';
$noticia=ij_post_by_slug($match[2],$type);
if (!$noticia) { $errorKind='publication';require __DIR__.'/404.php';return; }
$isOpinion=$type==='opinion';require __DIR__.'/noticia.php';
