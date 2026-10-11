<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/subscriptions.php';ij_subscriptions_ready();
$page=max(1,min(100000,(int)($_GET['pagina'] ?? 1)));$offset=($page-1)*50;
$total=(int)ij_db()->query('SELECT COUNT(*) FROM newsletter_subscribers')->fetchColumn();
$s=ij_db()->query('SELECT s.*, (SELECT MAX(accepted_at) FROM newsletter_consents c WHERE c.subscriber_id=s.id) accepted_at FROM newsletter_subscribers s ORDER BY requested_at DESC,id DESC LIMIT 50 OFFSET '.$offset);$subscribers=$s->fetchAll();
$pageTitle='Suscriptores del newsletter';require __DIR__.'/_head.php';
?>
<p><?= $total ?> solicitudes registradas. Las pendientes no se usarán para enviar newsletters hasta que se confirme el correo.</p><p><a class="button" href="/admin/newsletter.php">Volver a ediciones</a></p>
<?php if (!$subscribers): ?><p class="empty">Todavía no hay suscriptores registrados.</p><?php endif; ?>
<div class="post-list"><?php foreach ($subscribers as $subscriber): ?><article class="post-row"><div><h2><?= ij_e($subscriber['email']) ?></h2><p><?= ij_e(['pending'=>'Pendiente de confirmar','active'=>'Confirmado','unsubscribed'=>'Dado de baja'][$subscriber['status']] ?? 'Pendiente') ?> · Solicitud: <?= ij_date($subscriber['requested_at']) ?></p><?php if ($subscriber['accepted_at']): ?><p>Consentimiento registrado: <?= ij_date($subscriber['accepted_at']) ?></p><?php endif; ?></div></article><?php endforeach; ?></div>
<div class="toolbar"><?php if ($page>1): ?><a class="button" href="?pagina=<?= $page-1 ?>">Anterior</a><?php endif; ?><?php if ($offset+50<$total): ?><a class="button" href="?pagina=<?= $page+1 ?>">Siguiente</a><?php endif; ?></div>
<?php require __DIR__.'/_foot.php'; ?>
