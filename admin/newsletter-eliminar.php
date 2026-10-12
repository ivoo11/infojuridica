<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/mail.php';ij_mail_ready();
$id=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);$edition=$id ? ij_newsletter((int)$id) : null;
if(!$edition){http_response_code(404);exit('Edición no encontrada.');}
$error='';$db=ij_db();
if($_SERVER['REQUEST_METHOD']==='POST'){
 ij_check_csrf();$db->beginTransaction();
 try{
  // Same row lock as campaign preparation: a concurrent send cannot race deletion.
  $s=$db->prepare('SELECT id FROM newsletters WHERE id=? FOR UPDATE');$s->execute([$id]);
  if(!$s->fetchColumn())throw new InvalidArgumentException('Esta edición ya no existe.');
  $s=$db->prepare('SELECT id FROM newsletter_campaigns WHERE edition_id=?');$s->execute([$id]);
  if($s->fetchColumn())throw new InvalidArgumentException('Esta edición tiene un envío preparado. Se conserva para mantener la cola y el historial de envíos.');
  $s=$db->prepare('DELETE FROM newsletters WHERE id=?');$s->execute([$id]);$db->commit();
  header('Location: /admin/newsletter.php?eliminada=1');exit;
 }catch(InvalidArgumentException $e){if($db->inTransaction())$db->rollBack();$error=$e->getMessage();}
}
$s=$db->prepare('SELECT id FROM newsletter_campaigns WHERE edition_id=?');$s->execute([$id]);$campaign=$s->fetchColumn();
$pageTitle='Eliminar edición';require __DIR__.'/_head.php';
?>
<h2><?= ij_e($edition['subject']) ?></h2>
<?php if($error):?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif;?>
<?php if($campaign):?><p>Esta edición tiene un envío preparado. Se conserva junto con su historial.</p><a class="button" href="/admin/newsletter.php">Volver a ediciones</a>
<?php else:?>
<p>Se eliminará esta edición de la base de datos. Las noticias y los suscriptores se conservan. Esta acción no se puede deshacer.</p>
<form method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><div class="publish-actions"><button class="primary" type="submit">Eliminar edición</button><a class="button" href="/admin/newsletter.php">Cancelar</a></div></form>
<?php endif;require __DIR__.'/_foot.php'; ?>
