<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/home-slots.php';
$slots=ij_home_slots();$news=ij_posts('news');$byId=[];foreach ($news as $post) $byId[$post['id']]=$post;$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    ij_check_csrf();foreach (ij_slot_names() as $key=>$label) $slots[$key]=ij_field($key);
    try { ij_save_home_slots($slots);header('Location: /admin/portada.php?guardado=1');exit; }
    catch (InvalidArgumentException $e) { $error=$e->getMessage(); }
}
$pageTitle='Portada';require __DIR__.'/_head.php';
?>
<p>Elegí la noticia principal y las tres del lateral. Esta selección permanece fija hasta que vos la cambies.</p>
<?php if (isset($_GET['guardado'])): ?><p class="success" role="status">Selección de portada guardada.</p><?php endif; ?>
<form class="editor" method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><?php if ($error): ?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif; ?>
<?php foreach (ij_slot_names() as $key=>$label): $value=$slots[$key] ?? ''; ?><label><?= ij_e($label) ?><select name="<?= $key ?>"><option value="">Sin noticia seleccionada</option><?php foreach ($news as $post): ?><option value="<?= $post['id'] ?>" <?= (string)$value===(string)$post['id']?'selected':'' ?>><?= ij_e($post['titulo']) ?> · <?= ij_date($post['fecha']) ?></option><?php endforeach; ?></select><?php if ($value && !isset($byId[$value])): ?><span class="help">La noticia seleccionada ya no está visible. Esta posición no se completa automáticamente; elegí otra o dejala vacía.</span><?php endif; ?></label><?php endforeach; ?>
<p class="help">Todas las noticias publicadas aparecen también en Últimas noticias, ordenadas por fecha. Las posiciones vacías no se completan con otras noticias.</p><div class="toolbar"><button class="primary">Guardar portada</button><a class="button" id="cancelPortadaChanges" href="/admin/">Cancelar cambios</a></div></form>
<script defer src="/assets/js/portada.js?v=<?= filemtime(__DIR__.'/../assets/js/portada.js') ?>"></script>
<?php require __DIR__.'/_foot.php'; ?>
