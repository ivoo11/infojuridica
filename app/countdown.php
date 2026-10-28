<?php
require_once __DIR__.'/settings.php';
function ij_countdown(): array {
    $defaults=['enabled'=>false,'title'=>'Elecciones del Consejo de la Magistratura de la Ciudad','target'=>'','url'=>'','label'=>'Cómo votar'];
    if (!ij_live()) return $defaults;
    ij_settings_ready();
    $s=ij_db()->prepare('SELECT setting_value FROM site_settings WHERE setting_key=?');$s->execute(['countdown']);
    $value=$s->fetchColumn();$data=$value===false?[]:json_decode($value,true);
    return array_replace($defaults,is_array($data)?$data:[]);
}
function ij_countdown_target(string $value): ?DateTimeImmutable {
    if ($value==='') return null;
    $date=DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$value,new DateTimeZone('America/Argentina/Buenos_Aires'));
    if (!$date || $date->format('Y-m-d\TH:i')!==$value) throw new InvalidArgumentException('Revisá la fecha y la hora del evento.');
    return $date;
}
function ij_save_countdown(array $item): void {
    if (mb_strlen($item['title'])>180 || trim($item['title'])==='') throw new InvalidArgumentException('Escribí un título de hasta 180 caracteres.');
    ij_countdown_target($item['target']);
    if (mb_strlen($item['url'])>2000 || ($item['url']!=='' && !ij_link_url($item['url']))) throw new InvalidArgumentException('Usá un enlace completo que empiece con https:// o http://.');
    if (mb_strlen($item['label'])>60 || ($item['url']!=='' && trim($item['label'])==='')) throw new InvalidArgumentException('Escribí un texto de hasta 60 caracteres para el botón.');
    ij_settings_ready();
    $s=ij_db()->prepare('INSERT INTO site_settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
    $s->execute(['countdown',json_encode($item,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)]);
}
