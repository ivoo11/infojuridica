<?php
require __DIR__.'/../app/admin.php'; $user=ij_require_user();
require __DIR__.'/../app/events.php'; ij_events_ready();
$id=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);
if ($id===false || $id<0) { http_response_code(404); exit('Actividad no encontrada.'); }
$event=['title'=>'','description'=>'','starts_at'=>null,'ends_at'=>null,'modality'=>'presencial','place'=>'','registration_url'=>'','image'=>'','image_alt'=>'','status'=>'draft'];
if ($id) { $s=ij_db()->prepare('SELECT * FROM events WHERE id=?');$s->execute([$id]);$event=$s->fetch();if (!$event) { http_response_code(404);exit('Actividad no encontrada.'); } }
$originalStatus=$event['status'];$error='';
$fields=$event;$fields['days']=ij_event_days($event) ?: [['event_date'=>'','start_time'=>'','end_time'=>'']];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    ij_check_csrf();
    foreach (['title','description','modality','place','registration_url','image_alt'] as $field) $fields[$field]=ij_field($field);
    $fields['days']=is_array($_POST['days'] ?? null) ? $_POST['days'] : [];
    try {
        $days=ij_validate_event_days($fields['days']);
        $values=ij_event_values($fields,ij_field('action'));
        $image=ij_upload('image');$values['image']=$image ?: $event['image'];
        $columns=array_keys($values);$args=array_values($values);
        if ($id) { $sql='UPDATE events SET '.implode(',',array_map(fn($c)=>$c.'=?',$columns)).',updated_at=UTC_TIMESTAMP() WHERE id=?';$args[]=$id; }
        else $sql='INSERT INTO events ('.implode(',',$columns).') VALUES ('.implode(',',array_fill(0,count($columns),'?')).')';
        $db=ij_db();$db->beginTransaction();
        try {
            $db->prepare($sql)->execute($args);
            $savedId=$id ?: (int)$db->lastInsertId();
            $db->prepare('DELETE FROM event_days WHERE event_id=?')->execute([$savedId]);
            $insert=$db->prepare('INSERT INTO event_days(event_id,event_date,start_time,end_time) VALUES (?,?,?,?)');
            foreach ($days as $day) $insert->execute([$savedId,$day['event_date'],$day['start_time'] ?: null,$day['end_time'] ?: null]);
            $db->commit();
        } catch (Throwable $e) { $db->rollBack();throw $e; }
        header('Location: /admin/agenda.php?guardado=1');exit;
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
<fieldset><legend>Fechas y horarios</legend>
<p class="help">Agregá una jornada por día. Los horarios son de Argentina (GMT-3); podés indicar solo el inicio o dejar ambos sin completar.</p>
<div id="eventDays">
<?php foreach ($fields['days'] as $index=>$day): ?>
<div class="event-day-row">
<label>Fecha<input type="date" name="days[<?= (int)$index ?>][event_date]" value="<?= ij_e(is_string($day['event_date'] ?? null) ? $day['event_date'] : '') ?>"></label>
<label>Desde (opcional)<input type="time" name="days[<?= (int)$index ?>][start_time]" value="<?= ij_e(is_string($day['start_time'] ?? null) ? $day['start_time'] : '') ?>"></label>
<label>Hasta (opcional)<input type="time" name="days[<?= (int)$index ?>][end_time]" value="<?= ij_e(is_string($day['end_time'] ?? null) ? $day['end_time'] : '') ?>"></label>
<button type="button" data-remove-day aria-label="Quitar jornada">Quitar</button>
</div>
<?php endforeach; ?>
</div>
<button type="button" id="addEventDay">Agregar otra fecha</button>
<p class="help">Ejemplo: 22 y 23, de 9 a 17 → agregá las dos fechas y el horario de cada una. Si no conocés el cierre, dejá “Hasta” vacío.</p>
</fieldset>
<fieldset><legend>Lugar e inscripción</legend>
<label>Modalidad<select name="modality"><?php foreach (['presencial'=>'Presencial','virtual'=>'Virtual','hibrida'=>'Híbrida'] as $key=>$label): ?><option value="<?= $key ?>" <?= $fields['modality']===$key?'selected':'' ?>><?= $label ?></option><?php endforeach; ?></select></label>
<label>Lugar o plataforma<input name="place" maxlength="300" value="<?= ij_e($fields['place']) ?>"><span class="help">Por ejemplo: Facultad de Derecho, salón de actos; o Zoom.</span></label>
<label>Enlace de inscripción (opcional)<input type="url" name="registration_url" maxlength="2000" value="<?= ij_e($fields['registration_url']) ?>"></label></fieldset>
<fieldset><legend>Imagen (opcional)</legend><?php if ($event['image']): ?><img class="image-preview" src="<?= ij_e($event['image']) ?>" alt="Imagen guardada"><?php endif; ?>
<label>Subir o reemplazar imagen<input type="file" name="image" accept="image/jpeg,image/png,image/webp"><span class="help">JPG, PNG o WebP. Hasta 5 MB y 20 megapíxeles.</span></label>
<label>Descripción de la imagen<input name="image_alt" maxlength="240" value="<?= ij_e($fields['image_alt']) ?>"></label></fieldset>
<div class="publish-actions"><button class="primary" name="action" value="publish"><?= $originalStatus==='published'?'Guardar cambios':'Publicar ahora' ?></button><button name="action" value="draft"><?= $originalStatus==='published'?'Pasar a borrador':'Guardar borrador' ?></button><a id="cancelChanges" class="button" href="/admin/agenda.php">Cancelar cambios</a></div>
</form>
<script src="/assets/js/event-days.js?v=<?= filemtime(__DIR__.'/../assets/js/event-days.js') ?>"></script>
<script src="/assets/js/editor.js?v=<?= filemtime(__DIR__.'/../assets/js/editor.js') ?>"></script>
<?php require __DIR__.'/_foot.php'; ?>
