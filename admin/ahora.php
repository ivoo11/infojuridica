<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/settings.php';
$item=ij_ahora();$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    ij_check_csrf();$item=['texto'=>ij_field('texto'),'url'=>ij_field('url')];
    try { ij_save_ahora($item['texto'],$item['texto']==='' ? '' : $item['url']);header('Location: /admin/ahora.php?guardado=1');exit; }
    catch (InvalidArgumentException $e) { $error=$e->getMessage(); }
}
$pageTitle='Barra AHORA';require __DIR__.'/_head.php';
?>
<p>El aviso aparece debajo de la navegación de la portada. Dejá el texto vacío para ocultarlo por completo.</p>
<?php if (isset($_GET['guardado'])): ?><p class="success" role="status"><?= trim($item['texto'])===''?'Barra oculta.':'Aviso actualizado.' ?></p><?php endif; ?>
<form class="editor" method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><?php if ($error): ?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif; ?><label>Texto del aviso<textarea name="texto" rows="3" maxlength="300"><?= ij_e($item['texto']) ?></textarea><span class="help">Hasta 300 caracteres. Vacío: la barra desaparece y no deja espacio.</span></label><label>Enlace (opcional)<input name="url" maxlength="2000" value="<?= ij_e($item['url']) ?>" placeholder="/padron/ o https://..."><span class="help">Sin enlace, el aviso se muestra como texto.</span></label><div class="toolbar"><button class="primary">Guardar aviso</button><a class="button" id="cancelAhoraChanges" href="/admin/">Cancelar cambios</a></div></form>
<script defer src="/assets/js/ahora.js?v=<?= filemtime(__DIR__.'/../assets/js/ahora.js') ?>"></script>
<?php require __DIR__.'/_foot.php'; ?>
