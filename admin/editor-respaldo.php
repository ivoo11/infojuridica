<?php
require __DIR__.'/../app/admin.php';require __DIR__.'/../app/editor-recovery.php';
header('Content-Type: application/json; charset=utf-8');
$user=ij_user();if(!$user){http_response_code(401);echo json_encode(['error'=>'session_expired']);exit;}
$key=is_string($_GET['key'] ?? null)?$_GET['key']:'';
if(!ij_recovery_key($key)){http_response_code(400);echo json_encode(['error'=>'key']);exit;}
ij_recovery_ready();$db=ij_db();
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!is_string($_POST['csrf'] ?? null) || !hash_equals(ij_csrf(),$_POST['csrf'])){http_response_code(403);echo json_encode(['error'=>'csrf']);exit;}
 if(($_POST['discard'] ?? '')==='1'){ij_recovery_clear((int)$user['id'],$key);echo json_encode(['ok'=>true]);exit;}
 $raw=is_string($_POST['payload'] ?? null)?$_POST['payload']:'';$data=json_decode($raw,true);
 if(strlen($raw)>500000 || !is_array($data) || !is_array($data['fields'] ?? null) || !is_numeric($data['at'] ?? null)){http_response_code(422);echo json_encode(['error'=>'payload']);exit;}
 $allowed=['type','title','summary','body','category_id','author_id','image_alt','image_caption','slug','in_context','published_at','backdated_at','document_label','remove_document'];$fields=[];
 foreach($allowed as $field){if(isset($data['fields'][$field]) && is_string($data['fields'][$field]))$fields[$field]=$data['fields'][$field];}
 $payload=json_encode(['fields'=>$fields,'at'=>(int)$data['at'],'files'=>!empty($data['files'])],JSON_THROW_ON_ERROR);
 $s=$db->prepare('INSERT INTO editor_recovery(user_id,editor_key,payload) VALUES(?,?,?) ON DUPLICATE KEY UPDATE payload=VALUES(payload),saved_at=UTC_TIMESTAMP()');$s->execute([$user['id'],$key,$payload]);
 echo json_encode(['ok'=>true]);exit;
}
if($_SERVER['REQUEST_METHOD']!=='GET'){http_response_code(405);exit;}
$s=$db->prepare('SELECT payload FROM editor_recovery WHERE user_id=? AND editor_key=? AND saved_at>=DATE_SUB(UTC_TIMESTAMP(),INTERVAL 7 DAY)');$s->execute([$user['id'],$key]);$payload=$s->fetchColumn();
echo json_encode(['csrf'=>ij_csrf(),'recovery'=>$payload?json_decode($payload,true):null]);
