<?php
require_once __DIR__.'/content.php';
function ij_slot_names(): array { return ['lead'=>'Noticia principal','side1'=>'Lateral 1 · arriba','side2'=>'Lateral 2 · medio','side3'=>'Lateral 3 · abajo']; }
function ij_home_slots(): array {
    static $ready=false;
    $db=ij_db();
    if (!$ready) {
        $db->exec(file_get_contents(__DIR__.'/../database/home-slots.sql'));
        // Conservar solo destacados elegidos anteriormente; nunca completar con noticias recientes.
        $selected=array_values(array_filter(ij_posts('news'),fn($p)=>$p['featured']));
        usort($selected,fn($a,$b)=>($a['home_order']<=>$b['home_order']) ?: strcmp($b['fecha'],$a['fecha']) ?: ($b['id']<=>$a['id']));
        $lead=null;$side=[];
        foreach ($selected as $p) { if ($p['home_order']==0 && $lead===null) $lead=$p['id']; else $side[]=$p['id']; }
        $defaults=['lead'=>$lead,'side1'=>$side[0] ?? null,'side2'=>$side[1] ?? null,'side3'=>$side[2] ?? null];
        $s=$db->prepare('INSERT IGNORE INTO home_slots (slot_key,post_id) VALUES (?,?)');
        foreach ($defaults as $key=>$id) $s->execute([$key,$id]);
        $ready=true;
    }
    $rows=$db->query('SELECT slot_key,post_id FROM home_slots')->fetchAll();$slots=[];
    foreach ($rows as $row) $slots[$row['slot_key']]=$row['post_id']===null ? null : (int)$row['post_id'];
    return $slots;
}
function ij_save_home_slots(array $values): void {
    ij_home_slots();$valid=array_map(fn($p)=>(int)$p['id'],ij_posts('news'));$selected=[];$clean=[];
    foreach (ij_slot_names() as $key=>$label) {
        $value=$values[$key] ?? '';
        if ($value==='' || $value===null) { $clean[$key]=null;continue; }
        $id=filter_var($value,FILTER_VALIDATE_INT);
        if ($id===false || !in_array($id,$valid,true)) throw new InvalidArgumentException('Elegí noticias publicadas y visibles. Una noticia borrador o programada no puede ocupar la portada.');
        if (in_array($id,$selected,true)) throw new InvalidArgumentException('Una noticia no puede ocupar dos posiciones. Elegí una diferente o dejá la posición vacía.');
        $selected[]=$id;$clean[$key]=$id;
    }
    $db=ij_db();$db->beginTransaction();
    try {
        $db->query('SELECT slot_key FROM home_slots ORDER BY slot_key FOR UPDATE')->fetchAll();
        $s=$db->prepare('UPDATE home_slots SET post_id=? WHERE slot_key=?');foreach ($clean as $key=>$id) $s->execute([$id,$key]);$db->commit();
    } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack();throw $e; }
}
function ij_home_selection(array $news): array {
    $slots=ij_home_slots();$byId=[];foreach ($news as $p) $byId[$p['id']]=$p;
    $lead=$byId[$slots['lead'] ?? 0] ?? null;$side=[];
    foreach (['side1','side2','side3'] as $key) if (isset($byId[$slots[$key] ?? 0])) $side[]=$byId[$slots[$key]];
    return [$lead,$side];
}
