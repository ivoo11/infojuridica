<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__.'/../app/events.php';
$agendaEvents=ij_upcoming_events();
if (ij_live() && !$agendaEvents) return;
// Fechas de demostración para comprobar el carrusel. Sustituir por eventos verificados del CMS.
$agendaToday = new DateTimeImmutable('today', new DateTimeZone('America/Argentina/Buenos_Aires'));
$agendaMonths = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
?>
<section class="section home-agenda" id="agenda">
    <div class="container">
        <div class="section-header">
            <div><div class="section-kicker">Próximas actividades</div><h2 class="section-title">Agenda</h2><?php if (!ij_live()): ?><p class="home-demo-label">Actividades de demostración · Pendientes de contenido real</p><?php else: ?><a class="agenda-all-link" href="/agenda/">Todas las actividades</a><?php endif; ?></div>
            <div class="agenda-controls">
                <button type="button" id="agendaPrevious" aria-label="Actividad anterior" aria-controls="agendaTrack"></button>
                <button type="button" id="agendaNext" aria-label="Actividad siguiente" aria-controls="agendaTrack"></button>
            </div>
        </div>
        <div class="agenda-track" id="agendaTrack" tabindex="0" aria-label="Próximas actividades">
            <?php if (ij_live()): foreach ($agendaEvents as $event): $eventDate=new DateTimeImmutable(ij_event_local($event['starts_at'])); ?>
            <article class="agenda-item"><time class="agenda-day" datetime="<?= $eventDate->format('Y-m-d') ?>"><?= $eventDate->format('d') ?><span><?= $agendaMonths[(int)$eventDate->format('n')-1] ?></span></time><div><h3><a href="/agenda/actividad.php?id=<?= (int)$event['id'] ?>"><?= ij_e($event['title']) ?></a></h3><div class="agenda-place"><?= $eventDate->format('H:i') ?> · <?= ij_e($event['place']) ?></div></div></article>
            <?php endforeach; else: ?>
            <?php foreach ([7, 14, 21, 28] as $index => $days): $eventDate = $agendaToday->modify('+' . $days . ' days'); ?>
            <article class="agenda-item">
                <time class="agenda-day" datetime="<?= $eventDate->format('Y-m-d') ?>"><?= $eventDate->format('d') ?><span><?= $agendaMonths[(int) $eventDate->format('n') - 1] ?></span></time>
                <div><h3>Actividad de demostración <?= $index + 1 ?></h3><div class="agenda-place">Institución y lugar por incorporar</div></div>
            </article>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
