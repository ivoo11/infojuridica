<?php
// Comprobación CLI de solo lectura. No imprime credenciales ni contenido.
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$failed = false;
function check(string $label, bool $ok): void {
    global $failed;
    echo ($ok ? '[OK] ' : '[FALTA] ') . $label . PHP_EOL;
    if (!$ok) $failed = true;
}
check('PHP 8.3 o posterior', PHP_VERSION_ID >= 80300);
foreach (['pdo_mysql', 'mbstring', 'fileinfo', 'gd'] as $extension) check('Extensión ' . $extension, extension_loaded($extension));
check('Carpeta uploads escribible', is_writable(__DIR__ . '/../uploads'));
$configPath = getenv('INFOJURIDICA_CONFIG') ?: __DIR__ . '/../config/local.php';
check('Configuración privada presente', is_file($configPath));
if (!is_file($configPath)) exit(1);
$config = require $configPath;
check('Sin socket de MAMP en producción', empty($config['db']['socket']));
try {
    $c = $config['db'];
    $dsn = !empty($c['socket']) ? 'mysql:unix_socket=' . $c['socket'] : 'mysql:host=' . $c['host'] . ';port=' . (int) ($c['port'] ?? 3306);
    $db = new PDO($dsn . ';dbname=' . $c['name'] . ';charset=utf8mb4', $c['user'], $c['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    check('Conexión MySQL', true);
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach (['users','categories','authors','posts','login_attempts','site_settings','post_slugs','post_urls','home_slots','events','event_days'] as $table) check('Tabla ' . $table, in_array($table, $tables, true));
    if (in_array('users', $tables, true)) check('Usuario editorial activo', (int) $db->query('SELECT COUNT(*) FROM users WHERE active = 1')->fetchColumn() > 0);
    if (in_array('posts', $tables, true)) echo 'Publicaciones en la base: ' . $db->query('SELECT COUNT(*) FROM posts')->fetchColumn() . PHP_EOL;
} catch (Throwable $error) {
    check('Conexión y consultas MySQL (revisar configuración o permisos)', false);
}
echo 'El chequeo no modifica la base. HTTPS, rutas públicas y versión PHP web se verifican por separado.' . PHP_EOL;
exit($failed ? 1 : 0);
