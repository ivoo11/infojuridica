<?php
require_once __DIR__.'/bootstrap.php';
function ij_events_ready(): void {
    static $ready=false;
    if (!$ready) { ij_db()->exec(file_get_contents(__DIR__.'/../database/events.sql')); ij_db()->exec(file_get_contents(__DIR__.'/../database/event-days.sql')); $ready=true; }
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
    $days=ij_validate_event_days($input['days'] ?? []);
    $values['starts_at']=$days ? ij_event_date($days[0]['event_date'].'T'.($days[0]['start_time'] ?: '00:00')) : null;
    $last=$days ? $days[count($days)-1] : null;
    $values['ends_at']=$last ? ij_event_date($last['event_date'].'T'.($last['end_time'] ?: '23:59')) : null;
    if ($last && !$last['end_time']) $values['ends_at']=substr($values['ends_at'],0,-2).'59';
    if ($action==='publish' && (!$days || $values['description']==='' || $values['place']==='')) throw new InvalidArgumentException('Para publicar completá descripción, al menos una fecha y lugar o plataforma. Los horarios son opcionales.');
    $values['status']=$action==='publish' ? 'published' : 'draft';
    return $values;
}
function ij_upcoming_events(): array {
    if (!ij_live()) return [];
    ij_events_ready();
    // With no end supplied, keep the activity visible through its local calendar day.
    return ij_db()->query("SELECT * FROM events WHERE status='published' AND starts_at IS NOT NULL AND (ends_at >= UTC_TIMESTAMP() OR (ends_at IS NULL AND DATE(DATE_SUB(starts_at,INTERVAL 3 HOUR)) >= DATE(DATE_SUB(UTC_TIMESTAMP(),INTERVAL 3 HOUR)))) ORDER BY starts_at,id")->fetchAll();
}

function ij_validate_event_days(array $rows): array {
    if (count($rows)>60) throw new InvalidArgumentException('Podés cargar hasta 60 jornadas.');
    $days=[];
    foreach ($rows as $row) {
        if (!is_array($row)) throw new InvalidArgumentException('Revisá las jornadas.');
        $day=[];
        foreach (['event_date','start_time','end_time'] as $key) {
            if (!is_string($row[$key] ?? '')) throw new InvalidArgumentException('Revisá las jornadas.');
            $day[$key]=trim($row[$key] ?? '');
        }
        if (implode('',$day)==='') continue;
        $date=DateTimeImmutable::createFromFormat('!Y-m-d',$day['event_date']);
        if (!$date || $date->format('Y-m-d')!==$day['event_date']) throw new InvalidArgumentException('Cada jornada necesita una fecha válida.');
        foreach (['start_time','end_time'] as $key) if ($day[$key]!=='' && !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D',$day[$key])) throw new InvalidArgumentException('Revisá los horarios de las jornadas.');
        if ($day['end_time'] && $day['start_time'] && $day['end_time']<=$day['start_time']) throw new InvalidArgumentException('El cierre debe ser posterior al inicio de esa jornada. Si termina otro día, agregá esa fecha como otra jornada.');
        if (isset($days[$day['event_date']])) throw new InvalidArgumentException('Hay una fecha repetida. Cargá una jornada por día.');
        $days[$day['event_date']]=$day;
    }
    ksort($days); return array_values($days);
}
function ij_event_days(array $event): array {
    if (!empty($event['id'])) {
        $s=ij_db()->prepare('SELECT event_date,start_time,end_time FROM event_days WHERE event_id=? ORDER BY event_date');$s->execute([$event['id']]);$rows=$s->fetchAll();
        if ($rows) return array_map(fn($d)=>['event_date'=>$d['event_date'],'start_time'=>$d['start_time'] ? substr($d['start_time'],0,5) : '', 'end_time'=>$d['end_time'] ? substr($d['end_time'],0,5) : ''],$rows);
    }
    if (empty($event['starts_at'])) return [];
    $start=ij_event_local($event['starts_at']);$end=ij_event_local($event['ends_at'] ?? null);
    $first=['event_date'=>substr($start,0,10),'start_time'=>substr($start,11),'end_time'=>''];
    if ($end && substr($end,0,10)===$first['event_date']) $first['end_time']=substr($end,11);
    $days=[$first];
    if ($end && substr($end,0,10)!==$first['event_date']) $days[]=['event_date'=>substr($end,0,10),'start_time'=>'','end_time'=>substr($end,11)];
    return $days;
}
function ij_event_day_label(array $day): string {
    $label=ij_date($day['event_date']);
    if ($day['start_time']) $label.=$day['end_time'] ? ' · '.$day['start_time'].' a '.$day['end_time'] : ' · Desde las '.$day['start_time'];
    elseif ($day['end_time']) $label.=' · Hasta las '.$day['end_time'];
    return $label;
}
function ij_event_schedule_label(array $event): string {
    $days=ij_event_days($event);
    return $days ? implode(' / ',array_map('ij_event_day_label',$days)) : 'Fecha por confirmar';
}

function ij_event_card_schedule(array $days): array {
    if (!$days) return ['dates'=>'','hours'=>''];
    $first=new DateTimeImmutable($days[0]['event_date']);
    $last=new DateTimeImmutable($days[count($days)-1]['event_date']);
    $sameMonth=$first->format('Y-m')===$last->format('Y-m');
    $consecutive=true;
    foreach ($days as $i=>$day) if ($i && (new DateTimeImmutable($days[$i-1]['event_date']))->modify('+1 day')->format('Y-m-d')!==$day['event_date']) $consecutive=false;
    if ($sameMonth) {
        $numbers=array_map(fn($d)=>(string)(int)substr($d['event_date'],8,2),$days);
        if (count($days)>2 && $consecutive) $date=$numbers[0].'–'.$numbers[count($numbers)-1];
        elseif (count($days)>1) { $tail=array_pop($numbers);$date=implode(', ',$numbers).' y '.$tail; }
        else $date=$numbers[0];
        $date.=' '.substr(ij_date($days[0]['event_date']),3);
    } else $date=implode(' · ',array_map(fn($d)=>ij_date($d['event_date']),$days));
    $times=array_unique(array_map(fn($d)=>$d['start_time'].'|'.$d['end_time'],$days));
    if (count($times)>1) $hours='Ver horarios por jornada';
    else {
        $start=$days[0]['start_time'];$end=$days[0]['end_time'];
        $hours=$start ? ($end ? $start.' a '.$end : 'Desde las '.$start) : ($end ? 'Hasta las '.$end : '');
    }
    return ['dates'=>count($days)>1 ? $date : '', 'hours'=>$hours];
}
