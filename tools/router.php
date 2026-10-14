<?php
// Solo para pruebas con el servidor incorporado de PHP. MAMP usa .htaccess.
if (PHP_SAPI!=='cli-server') { http_response_code(404);exit; }
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if (str_starts_with($path,'/uploads/documents/')) { http_response_code(403);return true; }
if (preg_match('~^/(noticias|opinion)/[a-z0-9-]+/?$~D',$path)) { require __DIR__.'/../publication.php';return true; }
if (!is_file(__DIR__.'/..'.$path) && !is_dir(__DIR__.'/..'.$path)) { require __DIR__.'/../404.php';return true; }
return false;
