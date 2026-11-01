<section class="unsubscribe-panel" aria-labelledby="unsubscribe-title">
<?php if ($done || ($subscription && $subscription['status']==='unsubscribed')): ?>
<h1 id="unsubscribe-title">Tu baja quedó registrada.</h1>
<p role="status">No vas a recibir más newsletters de INFO JURÍDICA. Gracias por habernos acompañado.</p>
<div class="unsubscribe-actions"><a class="unsubscribe-primary" href="/">Volver al portal</a><a class="unsubscribe-secondary" href="/newsletter.php">Suscribirme de nuevo</a></div>
<?php elseif (!$subscription): ?>
<h1 id="unsubscribe-title">Este enlace ya no está disponible.</h1>
<p>Para gestionar tu suscripción, usá el enlace de baja del newsletter más reciente.</p>
<div class="unsubscribe-actions"><a class="unsubscribe-primary" href="/">Volver al portal</a></div>
<?php else: ?>
<h1 id="unsubscribe-title">¿Querés dejar de recibir el newsletter?</h1>
<p>Si confirmás la baja, dejaremos de enviarte nuestras ediciones. Podés volver a suscribirte cuando quieras.</p>
<form method="post" action="/newsletter-baja.php?token=<?= ij_e($token) ?>"><div class="unsubscribe-actions"><button type="submit" class="unsubscribe-primary">Sí, darme de baja</button><a class="unsubscribe-secondary" href="/">Seguir suscripto</a></div></form>
<?php endif; ?>
</section>
