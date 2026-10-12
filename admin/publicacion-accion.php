<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/home-slots.php';require_once __DIR__.'/../app/slugs.php';
ij_home_slots();ij_slugs_ready();
$id=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);$action=is_string($_GET['accion'] ?? null)?$_GET['accion']:'';
if(!$id || !in_array($action,['hide','delete'],true)){http_response_code(404);exit('Acción no encontrada.');}
$db=ij_db();$s=$db->prepare('SELECT * FROM posts WHERE id=?');$s->execute([$id]);$post=$s->fetch();
if(!$post){http_response_code(404);exit('Publicación no encontrada.');}
if($_SERVER['REQUEST_METHOD']==='POST'){
 ij_check_csrf();$db->beginTransaction();
 try{
  $s=$db->prepare('SELECT id FROM posts WHERE id=? FOR UPDATE');$s->execute([$id]);
  if(!$s->fetchColumn()){ $db->rollBack();header('Location: /admin/');exit; }
  if($action==='hide'){
   $s=$db->prepare("UPDATE posts SET status='draft',updated_at=UTC_TIMESTAMP() WHERE id=?");$s->execute([$id]);
  }else{
   $s=$db->prepare('UPDATE home_slots SET post_id=NULL WHERE post_id=?');$s->execute([$id]);
   $s=$db->prepare('DELETE FROM post_urls WHERE post_id=?');$s->execute([$id]);
   $s=$db->prepare('DELETE FROM post_slugs WHERE post_id=?');$s->execute([$id]);
   $s=$db->prepare('DELETE FROM posts WHERE id=?');$s->execute([$id]);
  }
  $db->commit();header('Location: /admin/?guardado='.($action==='hide'?'oculta':'eliminada'));exit;
 }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
$pageTitle=$action==='hide'?'Ocultar publicación':'Eliminar publicación';require __DIR__.'/_head.php';
?>
<h2><?= ij_e($post['title']) ?></h2>
<?php if($action==='hide'):?><p>La nota dejará de verse en el portal y quedará como borrador en el CMS. Su contenido y enlace se conservan para volver a publicarla.</p>
<?php else:?><p>Se eliminará la publicación de la base de datos y se retirará de la portada. Su enlace dejará de funcionar. Esta acción no se puede deshacer.</p><p class="help">Si está seleccionada en un borrador de newsletter, tendrás que reemplazarla antes de enviarlo. Los correos ya preparados o enviados conservan su contenido.</p><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><div class="publish-actions"><button type="submit" class="primary"><?= $action==='hide'?'Ocultar y pasar a borrador':'Eliminar definitivamente' ?></button><a class="button" href="/admin/">Cancelar</a></div></form>
<?php require __DIR__.'/_foot.php'; ?>
