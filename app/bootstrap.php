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
function ij_link_url(string $url): bool {
    $parts=parse_url($url);
    return strlen($url)<=2048 && !preg_match('/[\s<>"\x00-\x1f]/u',$url) && filter_var($url,FILTER_VALIDATE_URL) && is_array($parts) && in_array(strtolower($parts['scheme'] ?? ''),['http','https'],true) && !empty($parts['host']) && !isset($parts['user']) && !isset($parts['pass']);
}
function ij_inline(string $text,int $depth=0): string {
    if($depth>8)return ij_e($text);
    $pattern='~\[[^\]\r\n]+\]\(https?://[^\s<>()]+\)|\*\*[^\r\n*]+\*\*~iu';
    preg_match_all($pattern,$text,$matches,PREG_OFFSET_CAPTURE);$html='';$cursor=0;
    foreach($matches[0] as [$token,$offset]){
        $html.=ij_e(substr($text,$cursor,$offset-$cursor));
        if(str_starts_with($token,'**'))$html.='<strong>'.ij_inline(substr($token,2,-2),$depth+1).'</strong>';
        elseif(preg_match('~^\[([^\]\r\n]+)\]\((https?://[^\s<>()]+)\)$~iu',$token,$link) && ij_link_url($link[2]))$html.='<a href="'.ij_e($link[2]).'" target="_blank" rel="noopener noreferrer">'.ij_inline($link[1],$depth+1).'</a>';
        else $html.=ij_e($token);
        $cursor=$offset+strlen($token);
    }
    return $html.ij_e(substr($text,$cursor));
}
function ij_body(string $body): string {
    // Texto plano: párrafos, ## subtítulos y > citas; nunca ejecuta HTML editorial.
    $html = '';
    foreach (preg_split('/\R\s*\R/u', trim($body)) as $block) {
        if ($block === '') continue;
        if (str_starts_with($block, '## ')) $html .= '<h2>' . ij_inline(substr($block,3)) . '</h2>';
        elseif (str_starts_with($block, '> ')) $html .= '<blockquote>' . nl2br(ij_inline(preg_replace('/^> ?/m','',$block))) . '</blockquote>';
        else $html .= '<p>' . nl2br(ij_inline($block)) . '</p>';
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
