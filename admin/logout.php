<?php
require __DIR__.'/../app/admin.php';ij_require_user();
if ($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); exit; }
ij_check_csrf();$_SESSION=[];session_destroy();
setcookie(session_name(),'', ['expires'=>time()-3600,'path'=>'/','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','httponly'=>true,'samesite'=>'Lax']);
header('Location: /admin/login.php');
