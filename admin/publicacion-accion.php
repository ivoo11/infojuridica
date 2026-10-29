<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/home-slots.php';require_once __DIR__.'/../app/slugs.php';
ij_home_slots();ij_slugs_ready();
$id=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);$action=is_string($_GET['accion'] ?? null)?$_GET['accion']:'';
if(!$id || !in_array($action,['hide','draft','delete','publish'],true)){http_response_code(404);exit('Acción no encontrada.');}
$db=ij_db();$s=$db->prepare('SELECT * FROM posts WHERE id=?');$s->execute([$id]);$post=$s->fetch();
if(!$post){http_response_code(404);exit('Publicación no encontrada.');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 ij_check_csrf();$db->beginTransaction();
 try{
  $s=$db->prepare('SELECT * FROM posts WHERE id=? FOR UPDATE');$s->execute([$id]);
  if(!($post=$s->fetch())){ $db->rollBack();header('Location: /admin/');exit; }
  if($action==='draft'){
   $s=$db->prepare("UPDATE posts SET status='draft',updated_at=UTC_TIMESTAMP() WHERE id=?");$s->execute([$id]);
  }elseif(in_array($action,['hide','publish'],true)){
   if(!trim($post['title']) || !trim($post['summary']) || !trim($post['body']))throw new InvalidArgumentException('Completá título, bajada y cuerpo en el editor antes de compartir o publicar.');
   if($post['type']==='news' && !$post['image'])throw new InvalidArgumentException('Agregá una imagen en el editor antes de compartir la noticia.');
   if($post['type']==='opinion' && !$post['author_id'])throw new InvalidArgumentException('Elegí un autor en el editor antes de compartir la columna.');
   $date=$post['published_at'] && $post['published_at']<=ij_now() ? $post['published_at'] : ij_now();
   $s=$db->prepare("UPDATE posts SET status=?,published_at=?,updated_at=UTC_TIMESTAMP() WHERE id=?");$s->execute([$action==='hide'?'unlisted':'published',$date,$id]);
   $s=$db->prepare('SELECT slug FROM post_urls WHERE post_id=?');$s->execute([$id]);
   if(!$s->fetchColumn())ij_assign_slug($id,$post['title']);
  }else{
   $s=$db->prepare('UPDATE home_slots SET post_id=NULL WHERE post_id=?');$s->execute([$id]);
   $s=$db->prepare('DELETE FROM post_urls WHERE post_id=?');$s->execute([$id]);
   $s=$db->prepare('DELETE FROM post_slugs WHERE post_id=?');$s->execute([$id]);
   $s=$db->prepare('DELETE FROM posts WHERE id=?');$s->execute([$id]);
  }
  $db->commit();header('Location: /admin/?guardado='.(['draft'=>'borrador','hide'=>'oculta','delete'=>'eliminada','publish'=>'publicada'][$action]));exit;
 }catch(InvalidArgumentException $e){if($db->inTransaction())$db->rollBack();$error=$e->getMessage();}catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
$pageTitle=['draft'=>'Pasar a borrador privado','hide'=>'Ocultar publicación','delete'=>'Eliminar publicación','publish'=>'Publicar publicación'][$action];require __DIR__.'/_head.php';
?>
<h2><?= ij_e($post['title']) ?></h2>
<?php if($error):?><p class="error" role="alert"><?= ij_e($error) ?></p><a class="button primary" href="/admin/editar.php?id=<?= (int)$id ?>">Completar en el editor</a><?php endif;?>
<?php if($action==='publish'):?><p>Se publicará ahora. Si la nota ya tenía una fecha anterior, la conserva.</p>
<?php elseif($action==='hide'):?><p>La nota se retira de portada, categorías y búsqueda, pero cualquiera que tenga su enlace podrá leerla sin iniciar sesión. No es un borrador privado.</p>
<?php elseif($action==='draft'):?><p>La nota quedará privada en el CMS. Su enlace dejará de permitir la lectura hasta que la publiques o la guardes como oculta.</p><?php else:?><p>Se eliminará la publicación de la base de datos y se retirará de la portada. Su enlace dejará de funcionar. Esta acción no se puede deshacer.</p><p class="help">Si está seleccionada en un borrador de newsletter, tendrás que reemplazarla antes de enviarlo. Los correos ya preparados o enviados conservan su contenido.</p><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><div class="publish-actions"><button type="submit" class="primary"><?= ['draft'=>'Guardar como borrador privado','hide'=>'Ocultar · Mantener acceso por enlace','delete'=>'Eliminar definitivamente','publish'=>'Publicar ahora'][$action] ?></button><a class="button" href="/admin/">Cancelar</a></div></form>
<?php require __DIR__.'/_foot.php'; ?>
