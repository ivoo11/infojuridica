<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/countdown.php';
$item=ij_countdown();$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    ij_check_csrf();$item=['enabled'=>ij_field('enabled')==='1','title'=>ij_field('title'),'target'=>ij_field('target'),'url'=>ij_field('url'),'label'=>ij_field('label')];
    try {ij_save_countdown($item);header('Location: /admin/banner.php?guardado=1');exit;}
    catch (InvalidArgumentException $e) {$error=$e->getMessage();}
}
$pageTitle='Banner con cuenta regresiva';require __DIR__.'/_head.php';
?>
<p>Aparece entre las destacadas y Últimas noticias. Al llegar a la fecha indicada, se oculta solo. Guardá una nueva fecha futura para volver a mostrarlo.</p>
<?php if(isset($_GET['guardado'])):?><p class="success" role="status">Banner guardado.</p><?php endif;?>
<form class="editor" method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>">
<?php if($error):?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif;?>
<label class="check"><input type="checkbox" name="enabled" value="1" <?= $item['enabled']?'checked':'' ?>>Mostrar banner</label>
<label>Título<input name="title" required maxlength="180" value="<?= ij_e($item['title']) ?>"></label>
<label>Fecha y hora del evento<input type="datetime-local" name="target" value="<?= ij_e($item['target']) ?>"><span class="help">Sin fecha, o con una fecha pasada, el banner permanece oculto.</span></label>
<fieldset><legend>Botón opcional</legend><label>Enlace<input type="url" name="url" maxlength="2000" value="<?= ij_e($item['url']) ?>" placeholder="https://..."><span class="help">Dejalo vacío para mostrar únicamente la cuenta regresiva.</span></label><label>Texto del botón<input name="label" maxlength="60" value="<?= ij_e($item['label']) ?>"></label></fieldset>
<div class="toolbar"><button class="primary">Guardar banner</button><a class="button" id="cancelBannerChanges" href="/admin/portada.php">Cancelar cambios</a><a class="button" href="/" target="_blank" rel="noopener">Ver portada</a></div></form>
<script defer src="/assets/js/banner-editor.js?v=<?= filemtime(__DIR__.'/../assets/js/banner-editor.js') ?>"></script>
<?php require __DIR__.'/_foot.php'; ?>
