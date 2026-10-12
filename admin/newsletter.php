<?php
require __DIR__.'/../app/admin.php'; $user=ij_require_user();
require __DIR__.'/../app/mail.php'; ij_mail_ready();
$editions=ij_db()->query('SELECT n.id,n.subject,n.updated_at,c.id campaign_id FROM newsletters n LEFT JOIN newsletter_campaigns c ON c.edition_id=n.id ORDER BY n.updated_at DESC,n.id DESC')->fetchAll();
$pageTitle='Newsletter'; require __DIR__.'/_head.php';
?>
<div class="toolbar"><p>Prepará una edición con las noticias del portal y revisala antes de enviarla.</p><a class="button primary" href="/admin/newsletter-editar.php">Nueva edición</a></div>
<?php if (isset($_GET['guardado'])): ?><p class="success" role="status">Borrador guardado. No se envió ningún correo.</p><?php endif; ?>
<?php if(isset($_GET['eliminada'])): ?><p class="success" role="status">Edición eliminada.</p><?php endif; ?>
<p><a class="button" href="/admin/newsletter-suscriptores.php">Ver suscriptores</a></p>
<p class="help">Remitente: noreply@infojuridica.com.ar · Revisá la edición y abrí Enviar para preparar una prueba o el envío.</p>
<?php if (!$editions): ?><p class="empty">Todavía no hay ediciones. La primera se arma con las seis noticias más recientes; después podés cambiarlas.</p><?php endif; ?>
<div class="post-list"><?php foreach ($editions as $edition): ?><article class="post-row"><div><span class="tag"><?= $edition['campaign_id'] ? 'Envío preparado' : 'Borrador' ?></span><h2><a href="/admin/newsletter-editar.php?id=<?= (int)$edition['id'] ?>"><?= ij_e($edition['subject']) ?></a></h2><p>Última edición: <?= ij_date($edition['updated_at']) ?></p></div><div class="post-actions"><a class="button primary" href="/admin/newsletter-enviar.php?id=<?= (int)$edition['id'] ?>">Enviar</a><a class="button" href="/admin/newsletter-editar.php?id=<?= (int)$edition['id'] ?>">Editar</a><a class="button" href="/admin/newsletter-preview.php?id=<?= (int)$edition['id'] ?>" target="_blank" rel="noopener">Vista previa</a><?php if(!$edition['campaign_id']):?><a class="button button-danger" href="/admin/newsletter-eliminar.php?id=<?= (int)$edition['id'] ?>">Eliminar</a><?php endif;?></div></article><?php endforeach; ?></div>
<?php require __DIR__.'/_foot.php'; ?>
