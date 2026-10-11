<?php
require __DIR__.'/../app/events.php';
$isPreview=isset($_GET['preview']);
if ($isPreview) { require __DIR__.'/../app/admin.php';ij_require_user(); }
$id=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);
$event=null;
if (ij_live() && $id && $id>0) { ij_events_ready();$s=ij_db()->prepare("SELECT * FROM events WHERE id=?".($isPreview?'':" AND status='published'"));$s->execute([$id]);$event=$s->fetch(); }
if (!$event) { http_response_code(404);exit('Actividad no encontrada.'); }
$noticia=['titulo'=>$event['title'],'bajada'=>mb_substr($event['description'],0,160)];
require __DIR__.'/../includes/listing-head.php';require __DIR__.'/../includes/header.php';
?>
<main class="container event-detail">
<?php if ($isPreview): ?><p>Vista previa privada · <a href="/admin/actividad.php?id=<?= (int)$event['id'] ?>">Editar actividad</a></p><?php endif; ?>
<div class="section-kicker">Agenda</div><h1><?= ij_e($event['title']) ?></h1>
<ul class="event-schedule" aria-label="Jornadas de la actividad">
<?php foreach (ij_event_days($event) as $day): ?><li><?= ij_e(ij_event_day_label($day)) ?></li><?php endforeach; ?>
</ul>
<p><?= ij_e(['presencial'=>'Presencial','virtual'=>'Virtual','hibrida'=>'Híbrida'][$event['modality']]) ?> · <?= ij_e($event['place']) ?></p>
<?php if ($event['image']): ?><img class="event-image" src="<?= ij_e($event['image']) ?>" alt="<?= ij_e($event['image_alt']) ?>"><?php endif; ?>
<div class="event-description"><?= ij_body($event['description']) ?></div>
<div class="event-actions">
<?php if ($event['registration_url']): ?><a class="event-register" target="_blank" rel="noopener noreferrer" href="<?= ij_e($event['registration_url']) ?>">Inscribirme</a><?php endif; ?>
<a class="event-back" href="/agenda/">Ver próximas actividades</a>
</div>
</main>
<?php require __DIR__.'/../includes/footer.php'; ?>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__.'/../assets/js/app.js') ?>"></script>
<script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__.'/../assets/js/navigation.js') ?>"></script></body></html>
