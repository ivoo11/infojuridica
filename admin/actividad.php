<?php
require __DIR__.'/../app/admin.php'; $user=ij_require_user();
require __DIR__.'/../app/events.php'; ij_events_ready();
$id=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);
if ($id===false || $id<0) { http_response_code(404); exit('Actividad no encontrada.'); }
$event=['title'=>'','description'=>'','starts_at'=>null,'ends_at'=>null,'modality'=>'presencial','place'=>'','registration_url'=>'','image'=>'','image_alt'=>'','status'=>'draft'];
if ($id) { $s=ij_db()->prepare('SELECT * FROM events WHERE id=?');$s->execute([$id]);$event=$s->fetch();if (!$event) { http_response_code(404);exit('Actividad no encontrada.'); } }
$originalStatus=$event['status'];$error='';
$fields=$event;$fields['starts_at']=ij_event_local($event['starts_at']);$fields['ends_at']=ij_event_local($event['ends_at']);
if ($_SERVER['REQUEST_METHOD']==='POST') {
    ij_check_csrf();
    foreach (['title','description','starts_at','ends_at','modality','place','registration_url','image_alt'] as $field) $fields[$field]=ij_field($field);
    try {
        $values=ij_event_values($fields,ij_field('action'));
        $image=ij_upload('image');$values['image']=$image ?: $event['image'];
        $columns=array_keys($values);$args=array_values($values);
        if ($id) { $sql='UPDATE events SET '.implode(',',array_map(fn($c)=>$c.'=?',$columns)).',updated_at=UTC_TIMESTAMP() WHERE id=?';$args[]=$id; }
        else $sql='INSERT INTO events ('.implode(',',$columns).') VALUES ('.implode(',',array_fill(0,count($columns),'?')).')';
        ij_db()->prepare($sql)->execute($args);header('Location: /admin/agenda.php?guardado=1');exit;
    } catch (InvalidArgumentException $e) { $error=$e->getMessage(); }
}
$pageTitle=$id?'Editar actividad':'Nueva actividad';require __DIR__.'/_head.php';
?>
<form class="editor" method="post" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="<?= ij_csrf() ?>">
<?php if ($error): ?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif; ?>
<p><?= $originalStatus==='published'?'Esta actividad está publicada. Guardar cambios actualiza la información visible.':'Esta actividad no se muestra en el portal hasta publicarla.' ?></p>
<label>Título<input name="title" maxlength="240" required value="<?= ij_e($fields['title']) ?>"></label>
<label>Descripción<textarea id="articleBody" name="description" rows="9" maxlength="100000"><?= ij_e($fields['description']) ?></textarea></label>
<details id="bodyPreview"><summary>Ver formato de la descripción</summary><div class="editor-body-preview" id="bodyPreviewContent"></div></details>
<fieldset><legend>Cuándo y dónde</legend><p class="help">Fechas y horarios de Argentina (GMT-3). La finalización es opcional; si no la indicás, se muestra hasta terminar el día de inicio.</p>
<label>Inicio<input type="datetime-local" name="starts_at" value="<?= ij_e($fields['starts_at']) ?>"></label>
<label>Finalización (opcional)<input type="datetime-local" name="ends_at" value="<?= ij_e($fields['ends_at']) ?>"></label>
<label>Modalidad<select name="modality"><?php foreach (['presencial'=>'Presencial','virtual'=>'Virtual','hibrida'=>'Híbrida'] as $key=>$label): ?><option value="<?= $key ?>" <?= $fields['modality']===$key?'selected':'' ?>><?= $label ?></option><?php endforeach; ?></select></label>
<label>Lugar o plataforma<input name="place" maxlength="300" value="<?= ij_e($fields['place']) ?>"><span class="help">Por ejemplo: Facultad de Derecho, salón de actos; o Zoom.</span></label>
<label>Enlace de inscripción (opcional)<input type="url" name="registration_url" maxlength="2000" value="<?= ij_e($fields['registration_url']) ?>"></label></fieldset>
<fieldset><legend>Imagen (opcional)</legend><?php if ($event['image']): ?><img class="image-preview" src="<?= ij_e($event['image']) ?>" alt="Imagen guardada"><?php endif; ?>
<label>Subir o reemplazar imagen<input type="file" name="image" accept="image/jpeg,image/png,image/webp"><span class="help">JPG, PNG o WebP. Hasta 5 MB y 20 megapíxeles.</span></label>
<label>Descripción de la imagen<input name="image_alt" maxlength="240" value="<?= ij_e($fields['image_alt']) ?>"></label></fieldset>
<div class="publish-actions"><button class="primary" name="action" value="publish"><?= $originalStatus==='published'?'Guardar cambios':'Publicar ahora' ?></button><button name="action" value="draft"><?= $originalStatus==='published'?'Pasar a borrador':'Guardar borrador' ?></button><a id="cancelChanges" class="button" href="/admin/agenda.php">Cancelar cambios</a></div>
</form>
<script src="/assets/js/editor.js?v=<?= filemtime(__DIR__.'/../assets/js/editor.js') ?>"></script>
<?php require __DIR__.'/_foot.php'; ?>
