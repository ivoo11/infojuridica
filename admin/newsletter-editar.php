<?php
require __DIR__.'/../app/admin.php'; $user=ij_require_user();
require __DIR__.'/../app/newsletter.php'; ij_newsletters_ready();
$id=(int)($_GET['id'] ?? 0); $edition=$id ? ij_newsletter($id) : null;
if ($id && !$edition) { http_response_code(404);exit('La edición no existe.'); }
$available=ij_posts('news');
$values=$edition ?: ['subject'=>'Las últimas noticias de INFO JURÍDICA','introduction'=>'','ids'=>array_column(array_slice($available,0,6),'id'),'include_agenda'=>1];
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    ij_check_csrf();
    try {
        $action=ij_field('action');
        if (!in_array($action,['save','preview'],true)) throw new InvalidArgumentException('Elegí guardar borrador o vista previa.');
        $values=ij_newsletter_values($_POST,$available);
        $args=[$values['subject'],$values['introduction'],json_encode($values['ids'],JSON_THROW_ON_ERROR),$values['include_agenda']];
        if ($id) { $s=ij_db()->prepare('UPDATE newsletters SET subject=?,introduction=?,post_ids=?,include_agenda=?,updated_at=UTC_TIMESTAMP() WHERE id=?'); $s->execute([...$args,$id]); }
        else { $s=ij_db()->prepare('INSERT INTO newsletters(subject,introduction,post_ids,include_agenda) VALUES(?,?,?,?)');$s->execute($args);$id=(int)ij_db()->lastInsertId(); }
        header('Location: '.($action==='preview' ? '/admin/newsletter-preview.php?id='.$id : '/admin/newsletter.php?guardado=1'));exit;
    } catch (InvalidArgumentException $e) {
        $error=$e->getMessage();
        $values=['subject'=>ij_field('subject'),'introduction'=>ij_field('introduction'),'ids'=>array_slice(is_array($_POST['posts'] ?? null) ? $_POST['posts'] : [],0,6),'include_agenda'=>isset($_POST['include_agenda'])];
    }
}
$pageTitle=$id ? 'Editar newsletter' : 'Nueva edición'; require __DIR__.'/_head.php';
?>
<p>Elegí qué noticias reciben los suscriptores. Esta selección no cambia la portada del portal.</p>
<?php if ($error): ?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif; ?>
<form method="post" class="editor" id="newsletterEditor"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>">
<label>Asunto del correo<input name="subject" maxlength="190" required value="<?= ij_e($values['subject']) ?>"></label>
<label>Introducción <span class="help">Opcional. Un mensaje breve para abrir la edición.</span><textarea name="introduction" maxlength="3000" rows="4"><?= ij_e($values['introduction']) ?></textarea></label>
<fieldset><legend>Noticias de esta edición</legend><p class="help">La principal lleva foto y bajada. Las demás aparecen en el orden que elijas.</p>
<?php for ($slot=0;$slot<6;$slot++): $selected=(int)($values['ids'][$slot] ?? 0); ?><label><?= $slot===0 ? 'Noticia principal' : 'Noticia '.($slot+1).' (opcional)' ?><select name="posts[]" <?= $slot===0 ? 'required' : '' ?>>
<option value="0"><?= $slot===0 ? 'Elegí la principal' : 'No incluir una noticia aquí' ?></option>
<?php $found=false;foreach ($available as $post): if ((int)$post['id']===$selected) $found=true; ?><option value="<?= (int)$post['id'] ?>" <?= (int)$post['id']===$selected ? 'selected' : '' ?>><?= ij_e($post['title']) ?> · <?= ij_date($post['published_at']) ?></option><?php endforeach; ?>
<?php if ($selected && !$found): ?><option value="<?= $selected ?>" selected>Publicación no disponible: elegí otra</option><?php endif; ?>
</select></label><?php endfor; ?></fieldset>
<label class="check"><input type="checkbox" name="include_agenda" value="1" <?= $values['include_agenda'] ? 'checked' : '' ?>>Incluir próximas actividades</label><p class="help">Hasta tres actividades. Si no hay ninguna próxima, ese bloque no aparece.</p>
<div class="publish-actions"><button class="primary" name="action" value="save">Guardar borrador</button><button name="action" value="preview" formtarget="_blank">Guardar y ver preview</button></div>
<p class="help">La preview se abre en otra pestaña y guarda esta edición. Ninguna de estas acciones envía correos.</p>
<div class="editor-secondary-actions"><a class="button" id="cancelNewsletter" href="/admin/newsletter.php">Cancelar cambios</a></div>
</form>
<script src="/assets/js/newsletter.js?v=<?= filemtime(__DIR__.'/../assets/js/newsletter.js') ?>"></script>
<?php require __DIR__.'/_foot.php'; ?>
