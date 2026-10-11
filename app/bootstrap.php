<?php
declare(strict_types=1);
function ij_config(): array {
    static $config;
    if ($config === null) {
        $path = getenv('INFOJURIDICA_CONFIG') ?: __DIR__ . '/../config/local.php';
        $config = is_file($path) ? require $path : require __DIR__ . '/../config/example.php';
    }
    return $config;
}
function ij_live(): bool { return (bool) (ij_config()['enabled'] ?? false); }
function ij_db(): PDO {
    static $db;
    if (!$db) {
        $c = ij_config()['db'];
        $dsn = !empty($c['socket']) ? 'mysql:unix_socket=' . $c['socket'] : 'mysql:host=' . $c['host'] . ';port=' . (int) ($c['port'] ?? 3306);
        $db = new PDO($dsn . ';dbname=' . $c['name'] . ';charset=utf8mb4', $c['user'], $c['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]);
        $db->exec("SET time_zone = '+00:00'");
    }
    return $db;
}
function ij_e($value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function ij_now(): string { return gmdate('Y-m-d H:i:s'); }
function ij_date(string $date): string {
    $dateOnly = preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
    $d = new DateTimeImmutable($date, new DateTimeZone($dateOnly ? 'America/Argentina/Buenos_Aires' : 'UTC'));
    $d = $d->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
    $months = ['ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
    return $d->format('d') . ' ' . $months[(int)$d->format('n') - 1] . ' ' . $d->format('Y');
}
function ij_body(string $body): string {
    // Texto plano: párrafos, ## subtítulos y > citas; nunca ejecuta HTML editorial.
    $html = '';
    foreach (preg_split('/\R\s*\R/u', trim($body)) as $block) {
        if ($block === '') continue;
        if (str_starts_with($block, '## ')) $html .= '<h2>' . ij_e(substr($block,3)) . '</h2>';
        elseif (str_starts_with($block, '> ')) $html .= '<blockquote>' . nl2br(ij_e(preg_replace('/^> ?/m','',$block))) . '</blockquote>';
        else $html .= '<p>' . nl2br(ij_e($block)) . '</p>';
    }
    return $html;
}
$ijInitialBufferLevel = ob_get_level();
if (PHP_SAPI !== 'cli') ob_start();
set_exception_handler(function (Throwable $error) use ($ijInitialBufferLevel): void {
    while (ob_get_level() > $ijInitialBufferLevel) ob_end_clean();
    error_log('INFO JURIDICA: ' . $error->getMessage());
    http_response_code(503);
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>INFO JURÍDICA</title>';
    require __DIR__ . '/../includes/favicon.php';
    echo '</head><body><p>El sitio no está disponible en este momento. Intentá nuevamente en unos minutos.</p></body></html>';
});
