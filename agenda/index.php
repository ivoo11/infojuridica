<?php
require __DIR__.'/../app/events.php';$events=ij_upcoming_events();
$noticia=['titulo'=>'Agenda','bajada'=>'Próximas actividades de la comunidad jurídica.'];
require __DIR__.'/../includes/listing-head.php';require __DIR__.'/../includes/header.php';require __DIR__.'/../includes/navbar.php';
?>
<main class="container event-detail"><div class="section-kicker">Próximas actividades</div><h1>Agenda</h1>
<?php if (!$events): ?><p>Próximamente compartiremos nuevas actividades.</p><?php endif; ?>
<?php foreach ($events as $event): ?><article class="event-list-item"><h2><a href="/agenda/actividad.php?id=<?= (int)$event['id'] ?>"><?= ij_e($event['title']) ?></a></h2><p><?= ij_e(ij_date($event['starts_at']).' · '.substr(ij_event_local($event['starts_at']),11)) ?> · <?= ij_e($event['place']) ?></p></article><?php endforeach; ?></main>
<?php require __DIR__.'/../includes/footer.php'; ?>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__.'/../assets/js/app.js') ?>"></script>
<script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__.'/../assets/js/navigation.js') ?>"></script></body></html>
