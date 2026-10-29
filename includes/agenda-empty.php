<link rel="stylesheet" href="/assets/css/agenda-empty.css?v=<?= filemtime(__DIR__.'/../assets/css/agenda-empty.css') ?>">
<div class="agenda-empty">
<svg class="agenda-empty-calendar" width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true" focusable="false"><rect x="7" y="12" width="42" height="37" rx="3" stroke="currentColor" stroke-width="1.5"/><path d="M7 23h42M18 7v10M38 7v10M21 35h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
<div><?php if(!empty($agendaEmptyStandalone)):?><h2>La agenda se toma un respiro.</h2><?php else:?><h3>La agenda se toma un respiro.</h3><?php endif;?><p>Por ahora no hay próximas actividades. Cuando haya novedades, las vas a encontrar acá.</p></div>
</div>
