<?php
require __DIR__.'/../app/admin.php'; $user=ij_require_user();
require __DIR__.'/../app/events.php'; ij_events_ready();
$events=ij_db()->query('SELECT * FROM events ORDER BY starts_at DESC,id DESC')->fetchAll();
$pageTitle='Agenda'; require __DIR__.'/_head.php';
?>
<div class="list-header"><p>Administrá las actividades. Los borradores solo se ven en el panel; las actividades pasadas dejan de aparecer entre las próximas.</p><a class="button primary" href="/admin/actividad.php">Nueva actividad</a></div>
<?php if (isset($_GET['guardado'])): ?><p class="success" role="status">Actividad guardada.</p><?php endif; ?>
<div class="post-list">
<?php foreach ($events as $event): ?><article class="post-row"><div><h2><?= ij_e($event['title']) ?></h2><p><?= $event['status']==='draft'?'Borrador':'Publicada' ?> · <?= ij_e(ij_event_schedule_label($event)) ?></p></div><div><a class="button" href="/admin/actividad.php?id=<?= (int)$event['id'] ?>">Editar</a> <a class="button" target="_blank" rel="noopener" href="/agenda/actividad.php?id=<?= (int)$event['id'] ?>&preview=1">Vista previa</a></div></article><?php endforeach; ?>
<?php if (!$events): ?><p>Todavía no hay actividades. Creá la primera y guardala como borrador o publicala.</p><?php endif; ?>
</div>
<?php require __DIR__.'/_foot.php'; ?>
