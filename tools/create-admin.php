<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__.'/../app/bootstrap.php';
$sqlMode=($argv[1] ?? '')==='--sql';
$offset=$sqlMode ? 2 : 1;
$email=$argv[$offset] ?? '';$name=$argv[$offset+1] ?? 'Editor';
if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>190 || mb_strlen($name)>120) exit("Uso: php tools/create-admin.php correo nombre\n");
fwrite(STDOUT,"Contraseña (mínimo 12 caracteres; no se muestra): ");
$tty=function_exists('posix_isatty') && posix_isatty(STDIN);
if ($tty) system('stty -echo');
try { $password=rtrim(fgets(STDIN),"\r\n"); } finally { if ($tty) system('stty echo');fwrite(STDOUT,"\n"); }
if (strlen($password)<12 || strlen($password)>72) exit("Usá entre 12 y 72 bytes para la contraseña.\n");
if ($sqlMode) {
    $quote=fn($value)=>"'".str_replace("'","''",$value)."'";
    // Solo texto de correo/nombre simple en este modo para evitar escapes dependientes de SQL_MODE.
    if (str_contains($email, chr(92)) || str_contains($name, chr(92)) || preg_match('/[\x00-\x1f]/',$name)) exit("El nombre o correo contiene caracteres no admitidos para exportar SQL.\n");
    echo 'INSERT INTO users (email,name,password_hash) VALUES ('.implode(',',array_map($quote,[mb_strtolower($email),$name,password_hash($password,PASSWORD_DEFAULT)])).");\n";
    exit;
}
$s=ij_db()->prepare('INSERT INTO users (email,name,password_hash) VALUES (?,?,?)');
$s->execute([mb_strtolower($email),$name,password_hash($password,PASSWORD_DEFAULT)]);
echo "Usuario creado. Ingresá desde /admin/.\n";
