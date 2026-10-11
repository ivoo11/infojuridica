<?php
require_once __DIR__.'/bootstrap.php';
function ij_events_ready(): void {
    static $ready=false;
    if (!$ready) { ij_db()->exec(file_get_contents(__DIR__.'/../database/events.sql')); $ready=true; }
}
function ij_event_date(string $value): ?string {
    if ($value==='') return null;
    $date=DateTimeImmutable::createFromFormat('!Y-m-d\TH:i',$value,new DateTimeZone('America/Argentina/Buenos_Aires'));
    if (!$date || $date->format('Y-m-d\TH:i')!==$value) throw new InvalidArgumentException('Revisá la fecha y el horario de la actividad.');
    return $date->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
}
function ij_event_local(?string $date): string {
    return $date ? (new DateTimeImmutable($date,new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'))->format('Y-m-d\TH:i') : '';
}
function ij_event_values(array $input, string $action): array {
    if (!in_array($action,['draft','publish'],true)) throw new InvalidArgumentException('Elegí guardar borrador o publicar.');
    $values=[];
    foreach (['title'=>240,'description'=>100000,'place'=>300,'registration_url'=>2000,'image_alt'=>240] as $field=>$limit) {
        $values[$field]=trim($input[$field] ?? '');
        if (mb_strlen($values[$field])>$limit) throw new InvalidArgumentException('El campo '.$field.' supera la extensión permitida.');
    }
    if ($values['title']==='') throw new InvalidArgumentException('Completá el título de la actividad.');
    $values['modality']=$input['modality'] ?? '';
    if (!in_array($values['modality'],['presencial','virtual','hibrida'],true)) throw new InvalidArgumentException('Elegí una modalidad válida.');
    $url=$values['registration_url'];
    if ($url!=='' && (!filter_var($url,FILTER_VALIDATE_URL) || !in_array(strtolower(parse_url($url,PHP_URL_SCHEME) ?? ''),['http','https'],true))) throw new InvalidArgumentException('El enlace de inscripción debe comenzar con https:// o http://.');
    $values['starts_at']=ij_event_date($input['starts_at'] ?? '');
    $values['ends_at']=ij_event_date($input['ends_at'] ?? '');
    if ($values['ends_at'] && (!$values['starts_at'] || $values['ends_at']<$values['starts_at'])) throw new InvalidArgumentException('La finalización debe ser posterior o igual al inicio.');
    if ($action==='publish' && (!$values['starts_at'] || $values['description']==='' || $values['place']==='')) throw new InvalidArgumentException('Para publicar completá descripción, fecha, horario y lugar o plataforma.');
    $values['status']=$action==='publish' ? 'published' : 'draft';
    return $values;
}
function ij_upcoming_events(): array {
    if (!ij_live()) return [];
    ij_events_ready();
    // With no end supplied, keep the activity visible through its local calendar day.
    return ij_db()->query("SELECT * FROM events WHERE status='published' AND starts_at IS NOT NULL AND (ends_at >= UTC_TIMESTAMP() OR (ends_at IS NULL AND DATE(DATE_SUB(starts_at,INTERVAL 3 HOUR)) >= DATE(DATE_SUB(UTC_TIMESTAMP(),INTERVAL 3 HOUR)))) ORDER BY starts_at,id")->fetchAll();
}
