<?php
require_once __DIR__ . '/bootstrap.php';
header('Cache-Control: no-store, private');
header('X-Robots-Tag: noindex, nofollow');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
if (!ij_live()) { http_response_code(503); exit('El CMS todavía no está habilitado. Consultá docs/cms.md para configurarlo.'); }
ini_set('session.use_strict_mode','1');
ini_set('session.use_only_cookies','1');
session_name('ij_admin');
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off','httponly'=>true,'samesite'=>'Lax']);
session_start();
if (isset($_SESSION['last_seen']) && time()-$_SESSION['last_seen']>1800) { $_SESSION=[]; session_regenerate_id(true); }
$_SESSION['last_seen']=time();
function ij_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    $s=ij_db()->prepare('SELECT id,email,name FROM users WHERE id=? AND active=1');$s->execute([$_SESSION['user_id']]);
    return $s->fetch() ?: null;
}
function ij_require_user(): array {
    $u=ij_user();
    if (!$u) {
        $next=$_SERVER['REQUEST_URI'] ?? '';
        $returnToEditor=($_SERVER['REQUEST_METHOD'] ?? '')==='GET' && preg_match('~^/admin/editar\.php(?:\?(?:id=[1-9][0-9]*|recovery=[a-f0-9]{32}))?$~D',$next);
        header('Location: /admin/login.php'.($returnToEditor?'?next='.rawurlencode($next):''));exit;
    }
    return $u;
}
function ij_csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function ij_check_csrf(): void { if (!is_string($_POST['csrf'] ?? null) || !hash_equals(ij_csrf(),$_POST['csrf'])) { http_response_code(403); exit('La sesión del formulario venció. Volvé a cargar la página.'); } }
function ij_field(string $key): string { return is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : ''; }
function ij_upload(string $field): string {
    $f=$_FILES[$field] ?? null;
    if (!$f || $f['error']===UPLOAD_ERR_NO_FILE) return '';
    if ($f['error']!==UPLOAD_ERR_OK || $f['size']>5*1024*1024 || !is_uploaded_file($f['tmp_name'])) throw new InvalidArgumentException('La imagen debe pesar hasta 5 MB.');
    $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!in_array($mime,['image/jpeg','image/png','image/webp'],true)) throw new InvalidArgumentException('Usá una imagen JPG, PNG o WebP.');
    $info=getimagesize($f['tmp_name']);
    if (!$info || $info[0]>8000 || $info[1]>8000 || $info[0]*$info[1]>20000000) throw new InvalidArgumentException('La imagen excede el tamaño permitido (20 megapíxeles).');
    $source=imagecreatefromstring(file_get_contents($f['tmp_name']));
    if (!$source) throw new InvalidArgumentException('No se pudo leer la imagen.');
    $scale=min(1,2000/max($info[0],$info[1]));
    $target=imagecreatetruecolor(max(1,(int)($info[0]*$scale)),max(1,(int)($info[1]*$scale)));
    $white=imagecolorallocate($target,255,255,255);imagefill($target,0,0,$white);
    imagecopyresampled($target,$source,0,0,0,0,imagesx($target),imagesy($target),$info[0],$info[1]);
    $name=bin2hex(random_bytes(16)).'.jpg';$directory=__DIR__.'/../uploads';
    if (!is_dir($directory) || !is_writable($directory) || !imagejpeg($target,$directory.'/'.$name,85)) throw new InvalidArgumentException('No se pudo guardar la imagen. Revisá los permisos de uploads.');
    imagedestroy($source);imagedestroy($target);
    return '/uploads/'.$name;
}
