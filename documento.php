<?php
require_once __DIR__.'/app/content.php';require_once __DIR__.'/app/documents.php';
$id=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);$post=null;
if(isset($_GET['preview'])){require_once __DIR__.'/app/admin.php';ij_require_user();if($id){$s=ij_db()->prepare('SELECT type FROM posts WHERE id=?');$s->execute([$id]);$type=$s->fetchColumn();if($type)$post=ij_post($id,$type,true);}}
elseif($id){$post=ij_post($id,'news') ?: ij_post($id,'opinion');}
$document=$post ? ij_document((int)$id) : null;$file=$document ? ij_document_file($document) : null;
if(!$file){require __DIR__.'/404.php';return;}
$download=($_GET['download'] ?? '')==='1';
header('Content-Type: application/pdf');header('X-Content-Type-Options: nosniff');header('X-Robots-Tag: noindex');header('Cache-Control: no-store');
header('Content-Disposition: '.($download?'attachment':'inline').'; filename="fallo-'.(int)$id.'.pdf"');header('Content-Length: '.filesize($file));readfile($file);
