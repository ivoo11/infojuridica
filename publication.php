<?php
require_once __DIR__.'/app/content.php';
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '',PHP_URL_PATH) ?? '');
if (!preg_match('~^/(noticias|opinion)/([a-z0-9-]+)/?$~D',$path,$match)) { http_response_code(404);exit('Publicación no encontrada.'); }
$type=$match[1]==='opinion' ? 'opinion' : 'news';
$noticia=ij_post_by_slug($match[2],$type);
if (!$noticia) { http_response_code(404);exit('Publicación no encontrada.'); }
$isOpinion=$type==='opinion';require __DIR__.'/noticia.php';
