<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/home-slots.php';require_once __DIR__.'/../app/slugs.php';
ij_home_slots();ij_slugs_ready();
$id=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);$action=is_string($_GET['accion'] ?? null)?$_GET['accion']:'';
if(!$id || !in_array($action,['hide','delete','publish'],true)){http_response_code(404);exit('Acción no encontrada.');}
$db=ij_db();$s=$db->prepare('SELECT * FROM posts WHERE id=?');$s->execute([$id]);$post=$s->fetch();
if(!$post){http_response_code(404);exit('Publicación no encontrada.');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 ij_check_csrf();$db->beginTransaction();
 try{
  $s=$db->prepare('SELECT * FROM posts WHERE id=? FOR UPDATE');$s->execute([$id]);
  if(!($post=$s->fetch())){ $db->rollBack();header('Location: /admin/');exit; }
  if($action==='hide'){
   $s=$db->prepare("UPDATE posts SET status='draft',updated_at=UTC_TIMESTAMP() WHERE id=?");$s->execute([$id]);
  }elseif($action==='publish'){
   if($post['status']!=='draft')throw new InvalidArgumentException('La publicación ya no es un borrador. Volvé al listado para revisar su estado.');
   if(!trim($post['title']) || !trim($post['summary']) || !trim($post['body']))throw new InvalidArgumentException('Completá título, bajada y cuerpo en el editor antes de publicar.');
   if($post['type']==='news' && !$post['image'])throw new InvalidArgumentException('Agregá una imagen en el editor antes de publicar la noticia.');
   if($post['type']==='opinion' && !$post['author_id'])throw new InvalidArgumentException('Elegí un autor en el editor antes de publicar la columna.');
   $date=$post['published_at'] && $post['published_at']<=ij_now() ? $post['published_at'] : ij_now();
   $s=$db->prepare("UPDATE posts SET status='published',published_at=?,updated_at=UTC_TIMESTAMP() WHERE id=?");$s->execute([$date,$id]);
   $s=$db->prepare('SELECT slug FROM post_urls WHERE post_id=?');$s->execute([$id]);
   if(!$s->fetchColumn())ij_assign_slug($id,$post['title']);
  }else{
   $s=$db->prepare('UPDATE home_slots SET post_id=NULL WHERE post_id=?');$s->execute([$id]);
   $s=$db->prepare('DELETE FROM post_urls WHERE post_id=?');$s->execute([$id]);
   $s=$db->prepare('DELETE FROM post_slugs WHERE post_id=?');$s->execute([$id]);
   $s=$db->prepare('DELETE FROM posts WHERE id=?');$s->execute([$id]);
  }
  $db->commit();header('Location: /admin/?guardado='.(['hide'=>'oculta','delete'=>'eliminada','publish'=>'publicada'][$action]));exit;
 }catch(InvalidArgumentException $e){if($db->inTransaction())$db->rollBack();$error=$e->getMessage();}catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
$pageTitle=['hide'=>'Ocultar publicación','delete'=>'Eliminar publicación','publish'=>'Publicar publicación'][$action];require __DIR__.'/_head.php';
?>
<h2><?= ij_e($post['title']) ?></h2>
<?php if($error):?><p class="error" role="alert"><?= ij_e($error) ?></p><a class="button primary" href="/admin/editar.php?id=<?= (int)$id ?>">Completar en el editor</a><?php endif;?>
<?php if($action==='publish'):?><p>Se publicará ahora. Si la nota ya tenía una fecha anterior, la conserva.</p>
<?php elseif($action==='hide'):?><p>La nota dejará de verse en el portal y quedará como borrador en el CMS. Su contenido y enlace se conservan para volver a publicarla.</p>
<?php else:?><p>Se eliminará la publicación de la base de datos y se retirará de la portada. Su enlace dejará de funcionar. Esta acción no se puede deshacer.</p><p class="help">Si está seleccionada en un borrador de newsletter, tendrás que reemplazarla antes de enviarlo. Los correos ya preparados o enviados conservan su contenido.</p><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><div class="publish-actions"><button type="submit" class="primary"><?= ['hide'=>'Ocultar y pasar a borrador','delete'=>'Eliminar definitivamente','publish'=>'Publicar ahora'][$action] ?></button><a class="button" href="/admin/">Cancelar</a></div></form>
<?php require __DIR__.'/_foot.php'; ?>
