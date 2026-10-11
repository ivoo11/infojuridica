<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/mail.php';ij_mail_ready();
$id=(int)($_GET['id'] ?? 0);$edition=ij_newsletter($id);if(!$edition){http_response_code(404);exit('La edición no existe.');}
$error='';$notice='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 ij_check_csrf();try{
  if(ij_field('action')==='test'){ij_mail_queue_test($id,ij_field('test_email'));$notice='Prueba agregada a la cola. El proceso de envío la enviará cuando corresponda.';}
  elseif(ij_field('action')==='send'){if(ij_field('confirm_send')!=='1')throw new InvalidArgumentException('Confirmá el envío a los suscriptores.');$count=ij_mail_queue_campaign($id);$notice='Edición preparada para '.$count.' suscriptores. Se enviará en tandas.';}
  else throw new InvalidArgumentException('Elegí una acción válida.');
 }catch(InvalidArgumentException $e){$error=$e->getMessage();}
}
$s=ij_db()->prepare('SELECT id FROM newsletter_campaigns WHERE edition_id=?');$s->execute([$id]);$campaign=$s->fetchColumn();
$active=(int)ij_db()->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status='active'")->fetchColumn();
$pageTitle='Enviar newsletter';require __DIR__.'/_head.php';
?>
<h2><?= ij_e($edition['subject']) ?></h2><p><a class="button" href="/admin/newsletter-preview.php?id=<?= $id ?>" target="_blank" rel="noopener">Revisar preview</a> <a class="button" href="/admin/newsletter.php">Volver a ediciones</a></p>
<?php if($error):?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif;?><?php if($notice):?><p class="success" role="status"><?= ij_e($notice) ?></p><?php endif;?>
<?php if(!ij_mail_enabled()):?><p class="error">El envío está deshabilitado. Completá config/mail.local.php en el servidor y configurá el proceso de envío según docs/newsletter-envios.md.</p><?php endif;?>
<form class="editor" method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><label>Enviar prueba a<input type="email" name="test_email" required maxlength="190" value="<?= ij_e($user['email']) ?>"></label><button name="action" value="test" <?= !ij_mail_enabled() ? 'disabled' : '' ?>>Enviar prueba</button><p class="help">La prueba no suscribe al destinatario y también cuenta dentro del límite diario.</p></form>
<?php if(!$campaign):?><form class="editor" method="post" style="margin-top:24px;"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><p><?= $active ?> suscriptores activos. Hasta 90 intentos cada 24 horas, uno por minuto.</p><label class="check"><input type="checkbox" name="confirm_send" value="1" required>Revisé la edición y quiero enviarla a los suscriptores.</label><button class="primary" name="action" value="send" <?= !ij_mail_enabled() || !$active ? 'disabled' : '' ?>>Enviar edición</button><p class="help">Se guarda una copia de esta edición para el envío. Modificar luego el borrador no cambia los correos preparados. Las bajas se comprueban antes de cada envío.</p></form><?php else:$s=ij_db()->prepare('SELECT status,COUNT(*) total FROM newsletter_mail_jobs WHERE campaign_id=? GROUP BY status');$s->execute([$campaign]);?><p class="success">Esta edición ya está preparada para enviar y no se duplicará.</p><ul><?php foreach($s as $row):?><li><?= ij_e(['queued'=>'Pendientes','sent'=>'Enviados','failed'=>'Fallidos: requieren revisión','processing'=>'En proceso: si persiste, revisar antes de reintentar','cancelled'=>'Cancelados por baja'][$row['status']] ?? $row['status']) ?>: <?= (int)$row['total'] ?></li><?php endforeach;?></ul><?php endif;?>
<?php require __DIR__.'/_foot.php'; ?>
