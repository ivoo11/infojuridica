<?php
require __DIR__.'/../app/admin.php'; $user=ij_require_user();
require __DIR__.'/../app/newsletter.php'; ij_newsletters_ready();
$editions=ij_db()->query('SELECT id,subject,updated_at FROM newsletters ORDER BY updated_at DESC,id DESC')->fetchAll();
$pageTitle='Newsletter'; require __DIR__.'/_head.php';
?>
<div class="toolbar"><p>Prepará una edición con las noticias del portal y revisala antes de enviarla.</p><a class="button primary" href="/admin/newsletter-editar.php">Nueva edición</a></div>
<?php if (isset($_GET['guardado'])): ?><p class="success" role="status">Borrador guardado. No se envió ningún correo.</p><?php endif; ?>
<p><a class="button" href="/admin/newsletter-suscriptores.php">Ver suscriptores</a></p>
<p class="help">Remitente: noreply@infojuridica.com.ar · Revisá la edición y abrí Enviar para preparar una prueba o el envío.</p>
<?php if (!$editions): ?><p class="empty">Todavía no hay ediciones. La primera se arma con las seis noticias más recientes; después podés cambiarlas.</p><?php endif; ?>
<div class="post-list"><?php foreach ($editions as $edition): ?><article class="post-row"><div><span class="tag">Borrador</span><h2><a href="/admin/newsletter-editar.php?id=<?= (int)$edition['id'] ?>"><?= ij_e($edition['subject']) ?></a></h2><p>Última edición: <?= ij_date($edition['updated_at']) ?></p></div><div class="post-actions"><a class="button primary" href="/admin/newsletter-enviar.php?id=<?= (int)$edition['id'] ?>">Enviar</a><a class="button" href="/admin/newsletter-editar.php?id=<?= (int)$edition['id'] ?>">Editar</a><a class="button" href="/admin/newsletter-preview.php?id=<?= (int)$edition['id'] ?>" target="_blank" rel="noopener">Vista previa</a></div></article><?php endforeach; ?></div>
<?php require __DIR__.'/_foot.php'; ?>
