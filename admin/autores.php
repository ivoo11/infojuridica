<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();$db=ij_db();
$id=filter_var($_GET['id'] ?? 0,FILTER_VALIDATE_INT);if ($id===false || $id<0) { http_response_code(404);exit('Autor no encontrado.'); }
$a=['name'=>'','role'=>'','photo'=>''];
if ($id) { $s=$db->prepare('SELECT * FROM authors WHERE id=?');$s->execute([$id]);$a=$s->fetch();if (!$a) { http_response_code(404);exit('Autor no encontrado.'); } }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    ij_check_csrf();$a['name']=ij_field('name');$a['role']=ij_field('role');
    try {
        if (!$a['name'] || mb_strlen($a['name'])>120 || mb_strlen($a['role'])>200) throw new InvalidArgumentException('Completá el nombre (hasta 120 caracteres) y cargo (hasta 200).');
        $photo=ij_upload('photo');if ($photo) $a['photo']=$photo;
        $s=$db->prepare($id ? 'UPDATE authors SET name=?,role=?,photo=? WHERE id=?' : 'INSERT INTO authors (name,role,photo) VALUES (?,?,?)');
        $values=[$a['name'],$a['role'],$a['photo']];if ($id) $values[]=$id;$s->execute($values);header('Location: /admin/autores.php?guardado=1');exit;
    } catch (InvalidArgumentException $e) { $error=$e->getMessage(); }
}
$authors=$db->query('SELECT * FROM authors ORDER BY name')->fetchAll();$pageTitle='Autores';require __DIR__.'/_head.php';
?>
<?php if (isset($_GET['guardado'])): ?><p class="success" role="status">Autor guardado.</p><?php endif; ?>
<form class="editor" method="post" enctype="multipart/form-data"><h2><?= $id?'Editar autor':'Nuevo autor' ?></h2><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><?php if ($error): ?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif; ?><label>Nombre<input name="name" value="<?= ij_e($a['name']) ?>" maxlength="120" required></label><label>Cargo o presentación<input name="role" value="<?= ij_e($a['role']) ?>" maxlength="200"></label><?php if ($a['photo']): ?><img class="image-preview" src="<?= ij_e($a['photo']) ?>" alt="Foto actual"><?php endif; ?><label>Fotografía<input type="file" name="photo" accept="image/jpeg,image/png,image/webp"><span class="help">JPG, PNG o WebP. Hasta 5 MB y 20 megapíxeles.</span></label><button class="primary">Guardar autor</button></form>
<div class="post-list"><?php foreach ($authors as $author): ?><article class="post-row"><div><h2><?= ij_e($author['name']) ?></h2><p><?= ij_e($author['role']) ?></p></div><a class="button" href="/admin/autores.php?id=<?= $author['id'] ?>">Editar</a></article><?php endforeach; ?></div>
<?php require __DIR__.'/_foot.php'; ?>
