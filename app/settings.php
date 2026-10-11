<?php
require_once __DIR__.'/bootstrap.php';
function ij_settings_ready(): void {
    static $ready=false;
    if (!$ready) { ij_db()->exec(file_get_contents(__DIR__.'/../database/settings.sql'));$ready=true; }
}
function ij_ahora(): array {
    $defaults=require __DIR__.'/home-settings.php';
    if (!ij_live()) return $defaults['ahora'] ?? ['texto'=>'','url'=>''];
    ij_settings_ready();
    $s=ij_db()->prepare('SELECT setting_value FROM site_settings WHERE setting_key=?');$s->execute(['ahora']);$value=$s->fetchColumn();
    return $value===false ? ($defaults['ahora'] ?? ['texto'=>'','url'=>'']) : json_decode($value,true,512,JSON_THROW_ON_ERROR);
}
function ij_save_ahora(string $text,string $url): void {
    if (mb_strlen($text)>300 || mb_strlen($url)>2000) throw new InvalidArgumentException('El texto admite hasta 300 caracteres y el enlace hasta 2000.');
    if ($url!=='' && !(str_starts_with($url,'/') && !str_starts_with($url,'//') && !str_contains($url,chr(92))) && !(filter_var($url,FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($url,PHP_URL_SCHEME) ?? ''),['http','https'],true))) throw new InvalidArgumentException('Usá un enlace http/https o una ruta como /padron/.');
    if (preg_match('/[\x00-\x20]/',$url)) throw new InvalidArgumentException('El enlace no puede contener espacios ni saltos de línea.');
    ij_settings_ready();
    $s=ij_db()->prepare('INSERT INTO site_settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
    $s->execute(['ahora',json_encode(['texto'=>$text,'url'=>$url],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)]);
}
