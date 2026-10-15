<?php
require __DIR__.'/../app/admin.php';
$next=is_string($_GET['next'] ?? null)?$_GET['next']:'';
if(!preg_match('~^/admin/editar\.php(?:\?(?:id=[1-9][0-9]*|recovery=[a-f0-9]{32}))?$~D',$next))$next='/admin/';
if (ij_user()) { header('Location: '.$next); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    ij_check_csrf();
    $key=hash('sha256',$_SERVER['REMOTE_ADDR'] ?? 'local');
    $db=ij_db();
    $db->beginTransaction();
    $s=$db->prepare('INSERT INTO login_attempts (key_hash,attempts,window_start) VALUES (?,0,UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE key_hash=key_hash');$s->execute([$key]);
    $s=$db->prepare('SELECT * FROM login_attempts WHERE key_hash=? FOR UPDATE');$s->execute([$key]);$attempt=$s->fetch();
    if (strtotime($attempt['window_start'].' UTC')<time()-900) { $s=$db->prepare('UPDATE login_attempts SET attempts=0,window_start=UTC_TIMESTAMP() WHERE key_hash=?');$s->execute([$key]);$attempt['attempts']=0; }
    if ($attempt['attempts']>=5) { $db->commit(); $error='Demasiados intentos. Esperá 15 minutos y volvé a intentar.'; http_response_code(429); }
    else {
        $s=$db->prepare('SELECT * FROM users WHERE email=? AND active=1');$s->execute([mb_strtolower(ij_field('email'))]);$found=$s->fetch();
        $valid=password_verify(is_string($_POST['password'] ?? null) ? $_POST['password'] : '',$found['password_hash'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi');
        if ($found && $valid) {
            $s=$db->prepare('DELETE FROM login_attempts WHERE key_hash=?');$s->execute([$key]);$db->commit();
            session_regenerate_id(true);$_SESSION['user_id']=$found['id'];$_SESSION['csrf']=bin2hex(random_bytes(32));
            header('Location: '.$next);exit;
        }
        $s=$db->prepare('UPDATE login_attempts SET attempts=attempts+1 WHERE key_hash=?');$s->execute([$key]);$db->commit();
        $error='El correo o la contraseña no son correctos.';
    }
}
$pageTitle='Ingresar al panel';require __DIR__.'/_head.php';
?>
<form class="editor login" method="post"><input type="hidden" name="csrf" value="<?= ij_csrf() ?>"><?php if ($error): ?><p class="error" role="alert"><?= ij_e($error) ?></p><?php endif; ?><label>Correo<input type="email" name="email" required autocomplete="username" maxlength="190"></label><label>Contraseña<input type="password" name="password" required autocomplete="current-password"></label><button class="primary">Ingresar</button></form>
<?php require __DIR__.'/_foot.php'; ?>
