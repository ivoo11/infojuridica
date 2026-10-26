<?php
require_once __DIR__.'/content.php';
function ij_slot_names(): array { return ['lead'=>'Noticia principal','side1'=>'Lateral 1 · arriba','side2'=>'Lateral 2 · medio','side3'=>'Lateral 3 · abajo']; }
function ij_home_slots(): array {
    static $ready=false;
    $db=ij_db();
    if (!$ready) {
        $db->exec(file_get_contents(__DIR__.'/../database/home-slots.sql'));
        // Preserve the existing selection on first activation; rotation starts with the next visible news item.
        $selected=array_values(array_filter(ij_posts('news'),fn($p)=>$p['featured']));
        usort($selected,fn($a,$b)=>($a['home_order']<=>$b['home_order']) ?: strcmp($b['fecha'],$a['fecha']) ?: ($b['id']<=>$a['id']));
        $lead=null;$side=[];
        foreach ($selected as $p) { if ($p['home_order']==0 && $lead===null) $lead=$p['id']; else $side[]=$p['id']; }
        $defaults=['lead'=>$lead,'side1'=>$side[0] ?? null,'side2'=>$side[1] ?? null,'side3'=>$side[2] ?? null];
        $s=$db->prepare('INSERT IGNORE INTO home_slots (slot_key,post_id) VALUES (?,?)');
        foreach ($defaults as $key=>$id) $s->execute([$key,$id]);
        $db->exec(file_get_contents(__DIR__.'/../database/home-automatic.sql'));
        $db->exec("INSERT IGNORE INTO site_settings(setting_key,setting_value) VALUES('home_auto_seen','')");
        $ready=true;
    }
    ij_sync_home_slots();
    $rows=$db->query('SELECT slot_key,post_id FROM home_slots ORDER BY slot_key')->fetchAll();$slots=[];
    foreach ($rows as $row) $slots[$row['slot_key']]=$row['post_id']===null ? null : (int)$row['post_id'];
    return $slots;
}
function ij_home_pins(): array {
    ij_home_slots();return ij_db()->query('SELECT slot_key FROM home_slot_pins')->fetchAll(PDO::FETCH_COLUMN);
}
function ij_sync_home_slots(bool $force=false): void {
    $db=ij_db();
    $db->beginTransaction();
    try {
        $rows=$db->query('SELECT slot_key,post_id FROM home_slots ORDER BY slot_key FOR UPDATE')->fetchAll();$slots=[];
        foreach($rows as $row)$slots[$row['slot_key']]=$row['post_id']===null?null:(int)$row['post_id'];
        $pins=$db->query('SELECT slot_key FROM home_slot_pins ORDER BY slot_key FOR UPDATE')->fetchAll(PDO::FETCH_COLUMN);
        $vacatedPin=false;
        foreach($pins as $key)if(empty($slots[$key])){$stmt=$db->prepare('DELETE FROM home_slot_pins WHERE slot_key=?');$stmt->execute([$key]);$vacatedPin=true;}
        $pins=array_values(array_filter($pins,fn($key)=>!empty($slots[$key])));
        $previous=$db->query("SELECT setting_value FROM site_settings WHERE setting_key='home_auto_seen' FOR UPDATE")->fetchColumn();
        // Read current eligible posts after taking the locks, avoiding stale concurrent rotations.
        $news=ij_posts('news');$ids=array_map(fn($p)=>(int)$p['id'],$news);$seen=$ids;sort($seen,SORT_NUMERIC);
        $known=$previous!==''?json_decode($previous,true):null;
        $newNews=is_array($known) && count(array_diff($seen,$known))>0;
        $removed=false;
        foreach($slots as $key=>$id)if($id && !in_array($key,$pins,true) && !in_array($id,$ids,true))$removed=true;
        if($force || $newNews || $vacatedPin || ($previous!=='' && $removed)){
            $reserved=[];
            foreach($pins as $key)if(!empty($slots[$key]))$reserved[]=$slots[$key];
            $queue=array_values(array_diff($ids,$reserved));
            $update=$db->prepare('UPDATE home_slots SET post_id=? WHERE slot_key=?');
            foreach(ij_slot_names() as $key=>$label){
                if(in_array($key,$pins,true) && !empty($slots[$key]))continue;
                $update->execute([array_shift($queue),$key]);
            }
        }
        $stmt=$db->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key='home_auto_seen'");$stmt->execute([json_encode($seen)]);
        $db->commit();
    }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
function ij_save_home_slots(array $values,array $pins=[]): void {
    $current=ij_home_slots();$valid=array_map(fn($p)=>(int)$p['id'],ij_posts('news'));$selected=[];$clean=[];
    foreach (ij_slot_names() as $key=>$label) {
        $value=$values[$key] ?? '';
        if ($value==='' || $value===null) { if(in_array($key,$pins,true))throw new InvalidArgumentException('Elegí una noticia antes de fijar su posición.');$clean[$key]=null;continue; }
        $id=filter_var($value,FILTER_VALIDATE_INT);
        $unchangedPin=in_array($key,$pins,true) && $id===($current[$key] ?? null);
        if ($id===false || (!in_array($id,$valid,true) && !$unchangedPin)) throw new InvalidArgumentException('Elegí noticias publicadas y visibles.');
        if (in_array($id,$selected,true)) throw new InvalidArgumentException('Una noticia no puede ocupar dos posiciones. Elegí una diferente o dejá la posición vacía.');
        $selected[]=$id;$clean[$key]=$id;
    }
    $db=ij_db();$db->beginTransaction();
    try {
        $db->query('SELECT slot_key FROM home_slots ORDER BY slot_key FOR UPDATE')->fetchAll();
        $db->query('SELECT slot_key FROM home_slot_pins ORDER BY slot_key FOR UPDATE')->fetchAll();
        $s=$db->prepare('UPDATE home_slots SET post_id=? WHERE slot_key=?');foreach ($clean as $key=>$id) $s->execute([$id,$key]);
        $db->exec('DELETE FROM home_slot_pins');$s=$db->prepare('INSERT INTO home_slot_pins(slot_key) VALUES(?)');
        foreach(array_keys(ij_slot_names()) as $key)if(in_array($key,$pins,true))$s->execute([$key]);
        $db->commit();
    } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack();throw $e; }
}
function ij_home_selection(array $news): array {
    $slots=ij_home_slots();$byId=[];foreach ($news as $p) $byId[$p['id']]=$p;
    $lead=$byId[$slots['lead'] ?? 0] ?? null;$side=[];
    foreach (['side1','side2','side3'] as $key) if (isset($byId[$slots[$key] ?? 0])) $side[]=$byId[$slots[$key]];
    return [$lead,$side];
}
