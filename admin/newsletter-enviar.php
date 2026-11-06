<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/mail.php';ij_mail_ready();
$id=(int)($_GET['id'] ?? 0);$edition=ij_newsletter($id);if(!$edition){http_response_code(404);exit('La edición no existe.');}
$error=$_SESSION['newsletter_send_error'] ?? '';unset($_SESSION['newsletter_send_error']);$notice='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 ij_check_csrf();try{
  if(ij_field('action')==='test'){if(!hash_equals($_SESSION['newsletter_test_nonce'][$id] ?? '',ij_field('test_nonce')) || empty($_SESSION['newsletter_test_nonce'][$id]))throw new InvalidArgumentException('Esta prueba ya fue solicitada. Revisá su estado antes de enviar otra.');unset($_SESSION['newsletter_test_nonce'][$id]);$job=ij_mail_queue_test($id,ij_field('test_email'));session_write_close();ij_mail_process(null,$job);header('Location: /admin/newsletter-enviar.php?id='.$id.'&prueba='.$job,true,303);exit;}
  elseif(ij_field('action')==='send'){if(ij_field('confirm_send')!=='1')throw new InvalidArgumentException('Confirmá el envío a los suscriptores.');$count=ij_mail_queue_campaign($id);header('Location: /admin/newsletter-enviar.php?id='.$id,true,303);exit;}
  else throw new InvalidArgumentException('Elegí una acción válida.');
 }catch(InvalidArgumentException $e){$_SESSION['newsletter_send_error']=$e->getMessage();header('Location: /admin/newsletter-enviar.php?id='.$id,true,303);exit;}
}
$s=ij_db()->prepare('SELECT id FROM newsletter_campaigns WHERE edition_id=?');$s->execute([$id]);$campaign=$s->fetchColumn();
$active=(int)ij_db()->query("SELECT COUNT(*) FROM newsletter_subscribers WHERE status='active'")->fetchColumn();
$testJob=filter_var($_GET['prueba'] ?? 0,FILTER_VALIDATE_INT);
if($testJob){$s=ij_db()->prepare("SELECT status FROM newsletter_mail_jobs WHERE id=? AND kind='test'");$s->execute([$testJob]);$testStatus=$s->fetchColumn();$notice=['sent'=>'La prueba fue enviada al servidor de correo. Revisá tu casilla.','queued'=>'La prueba quedó pendiente por el límite de envío o porque hay otro correo en proceso. El cron la enviará automáticamente.','failed'=>'No se confirmó el envío de la prueba. Revisá la conexión antes de volver a intentarlo.','processing'=>'La prueba está en proceso. Si este estado persiste, revisalo antes de reenviarla.'][$testStatus] ?? '';}
$_SESSION['newsletter_test_nonce'][$id] ??= bin2hex(random_bytes(16));
$pageTitle='Enviar newsletter';require __DIR__.'/_head.php';
?>
<h2><?= ij_e($edition['subject']) ?></h2><p><a class="button" href="/admin/newsletter-preview.php?id=<?= $id ?>" target="_blank" rel="noopener">Revisar preview</a> <a class="button" href="/admin/newsletter.php">Volver a ediciones</a></p>
<?php if($error):?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif;?><?php if($notice):?><p class="<?= ($testStatus ?? '')==='failed'?'error':(in_array($testStatus ?? '',['queued','processing'],true)?'help':'success') ?>" role="status"><?= ij_e($notice) ?></p><?php endif;?>
<?php if(!ij_mail_enabled()):?><p class="error">El envío está deshabilitado. Completá config/mail.local.php en el servidor y configurá el proceso de envío según docs/newsletter-envios.md.</p><?php endif;?>
<?php if($campaign):?><details style="margin-top:24px;"><summary style="cursor:pointer;padding:12px 0;">Enviar otra prueba</summary><?php endif;?>
<form class="editor" method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><input type="hidden" name="test_nonce" value="<?= ij_e($_SESSION['newsletter_test_nonce'][$id]) ?>"><label>Enviar prueba a<input type="email" name="test_email" required maxlength="190" value="<?= ij_e($user['email']) ?>"></label><button name="action" value="test" <?= !ij_mail_enabled() ? 'disabled' : '' ?>>Enviar prueba</button><p class="help">La prueba no suscribe al destinatario y también cuenta dentro del límite diario.</p></form>
<?php if($campaign):?></details><?php endif;?>
<?php if(!$campaign):?><form class="editor" method="post" style="margin-top:24px;"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><p><?= $active ?> suscriptores activos. Hasta <?= ij_mail_minute_limit() ?> correos por minuto y <?= ij_mail_daily_limit() ?> intentos cada 24 horas. El envío empieza al confirmar.</p><label class="check"><input type="checkbox" name="confirm_send" value="1" required>Revisé la edición y quiero enviarla a los suscriptores.</label><button class="primary" name="action" value="send" <?= !ij_mail_enabled() || !$active ? 'disabled' : '' ?>>Enviar edición</button><p class="help">Se guarda una copia de esta edición para el envío. Modificar luego el borrador no cambia los correos preparados. Las bajas se comprueban antes de cada envío.</p></form><?php else:$progress=ij_mail_campaign_status((int)$campaign);?>
<section class="editor newsletter-send-progress" data-newsletter-send data-edition="<?= $id ?>" data-csrf="<?= ij_e(ij_csrf()) ?>" style="margin-top:24px;">
<h2>Progreso del envío</h2><p data-send-message role="status">El envío continúa por tandas. Podés cerrar esta página: el cron retoma los pendientes.</p>
<progress data-send-bar max="<?= max(1,$progress['total']) ?>" value="<?= $progress['total']-$progress['counts']['queued']-$progress['counts']['processing'] ?>" aria-label="Progreso del envío" style="width:100%;height:16px;accent-color:#711b2d;"></progress>
<ul><?php foreach(['sent'=>'Enviados al servidor','queued'=>'Pendientes','processing'=>'En proceso','failed'=>'Fallidos: requieren revisión','cancelled'=>'Cancelados por baja'] as $key=>$label):?><li><?= $label ?>: <strong data-count="<?= $key ?>"><?= $progress['counts'][$key] ?></strong></li><?php endforeach;?></ul>
<p class="help">La edición preparada no se duplica. Los fallidos y los envíos interrumpidos no se reintentan automáticamente.</p><button type="button" data-send-resume hidden>Continuar envío</button>
<noscript><p>El cron continuará el envío automáticamente. Recargá esta página para ver el progreso.</p></noscript>
</section>
<script defer src="/assets/js/newsletter-send.js?v=<?= filemtime(__DIR__.'/../assets/js/newsletter-send.js') ?>"></script>
<?php endif;?>
<?php require __DIR__.'/_foot.php'; ?>
