<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/home-slots.php';
$slots=ij_home_slots();$pins=ij_home_pins();$news=ij_posts('news');$byId=[];foreach ($news as $post) $byId[$post['id']]=$post;$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    ij_check_csrf();$pins=[];foreach(array_keys(ij_slot_names()) as $key)if(ij_field('pin_'.$key)==='1')$pins[]=$key;foreach (ij_slot_names() as $key=>$label) $slots[$key]=ij_field($key);
    try { ij_save_home_slots($slots,$pins);if(ij_field('action')==='rotate')ij_sync_home_slots(true);header('Location: /admin/portada.php?guardado=1');exit; }
    catch (InvalidArgumentException $e) { $error=$e->getMessage(); }
}
$pageTitle='Portada';require __DIR__.'/_head.php';
?>
<p>La portada se actualiza con las noticias más recientes por fecha de publicación. Podés elegir otras a mano y fijar las posiciones que quieras conservar cuando haya novedades.</p>
<?php if (isset($_GET['guardado'])): ?><p class="success" role="status">Selección de portada guardada.</p><?php endif; ?>
<form class="editor" method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><?php if ($error): ?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif; ?>
<?php foreach (ij_slot_names() as $key=>$label): $value=$slots[$key] ?? ''; ?><fieldset><legend><?= ij_e($label) ?></legend><label>Noticia<select name="<?= $key ?>"><option value="">Sin noticia seleccionada</option><?php if($value && !isset($byId[$value])):?><option selected value="<?= (int)$value ?>">Noticia no visible · <?= (int)$value ?></option><?php endif;?><?php foreach ($news as $post): ?><option value="<?= $post['id'] ?>" <?= (string)$value===(string)$post['id']?'selected':'' ?>><?= ij_e($post['titulo']) ?> · <?= ij_date($post['fecha']) ?></option><?php endforeach; ?></select><?php if ($value && !isset($byId[$value])): ?><span class="help">La noticia seleccionada no está visible. Si la posición está fija, queda reservada hasta que vuelva a publicarse. Podés elegir otra noticia.</span><?php endif; ?></label><label class="check"><input type="checkbox" name="pin_<?= $key ?>" value="1" <?= in_array($key,$pins,true)?'checked':'' ?>>Fijar esta posición</label></fieldset><?php endforeach; ?>
<p class="help">Los cambios manuales sin fijar duran hasta la próxima noticia nueva. «Completar con las más recientes» actualiza ahora los lugares libres y respeta los fijos. Todas las noticias siguen apareciendo en Últimas noticias.</p><div class="toolbar"><button class="primary" name="action" value="save">Guardar portada</button><button name="action" value="rotate">Completar con las más recientes</button><a class="button" id="cancelPortadaChanges" href="/admin/">Cancelar cambios</a></div></form>
<script defer src="/assets/js/portada.js?v=<?= filemtime(__DIR__.'/../assets/js/portada.js') ?>"></script>
<?php require __DIR__.'/_foot.php'; ?>
