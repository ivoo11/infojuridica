<?php
require __DIR__.'/../app/admin.php';
header('Content-Type: application/json; charset=utf-8');
function ij_send_json(array $data,int $code=200): never {http_response_code($code);echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);exit;}
if(!ij_user())ij_send_json(['message'=>'La sesión venció. Volvé a iniciar sesión; el cron seguirá con los pendientes.'],401);
$method=$_SERVER['REQUEST_METHOD'];if(!in_array($method,['GET','POST'],true)){header('Allow: GET, POST');ij_send_json(['message'=>'Solicitud inválida.'],405);}
if($method==='POST' && (!is_string($_POST['csrf'] ?? null) || !hash_equals(ij_csrf(),$_POST['csrf'])))ij_send_json(['message'=>'Volvé a cargar la página para continuar.'],403);
require __DIR__.'/../app/mail.php';ij_mail_ready();
$id=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);$s=ij_db()->prepare('SELECT id FROM newsletter_campaigns WHERE edition_id=?');$s->execute([$id]);$campaign=$s->fetchColumn();
if(!$campaign)ij_send_json(['message'=>'No existe una campaña preparada para esta edición.'],404);
session_write_close();
$result=$method==='POST'?ij_mail_process(null,null,(int)$campaign):'status';
ij_send_json(['result'=>$result]+ij_mail_campaign_status((int)$campaign));
