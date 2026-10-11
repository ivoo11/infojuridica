<?php
require_once __DIR__.'/bootstrap.php';
function ij_slugify(string $title): string {
    $text=strtr(mb_strtolower($title),['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
    $text=preg_replace('/[^a-z0-9]+/','-',$text);
    return trim(substr(trim($text,'-'),0,180),'-') ?: 'publicacion';
}
function ij_slug_owner(string $slug): ?int {
    $s=ij_db()->prepare('SELECT post_id FROM post_slugs WHERE slug=?');$s->execute([$slug]);$id=$s->fetchColumn();
    return $id===false ? null : (int)$id;
}
function ij_assign_slug(int $id,string $title,string $requested=''): string {
    $auto=$requested==='';$slug=$auto ? ij_slugify($title) : trim($requested);
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D',$slug) || strlen($slug)>180) throw new InvalidArgumentException('El enlace admite letras minúsculas sin tildes, números y guiones, hasta 180 caracteres.');
    $base=$slug;$suffix=2;
    while (($owner=ij_slug_owner($slug))!==null && $owner!==$id) {
        if (!$auto) throw new InvalidArgumentException('Ese enlace ya pertenece a otra publicación. Elegí uno diferente.');
        $slug=rtrim(substr($base,0,170),'-').'-'.$suffix++;
    }
    if ($owner===null) {
        try { $s=ij_db()->prepare('INSERT INTO post_slugs (slug,post_id) VALUES (?,?)');$s->execute([$slug,$id]); }
        catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null)===1062) throw new InvalidArgumentException('Ese enlace acaba de ser utilizado. Probá con uno diferente.');
            throw $e;
        }
    }
    $s=ij_db()->prepare('INSERT INTO post_urls (post_id,slug) VALUES (?,?) ON DUPLICATE KEY UPDATE slug=VALUES(slug)');$s->execute([$id,$slug]);
    return $slug;
}
function ij_slugs_ready(): void {
    static $ready=false;
    if ($ready || !ij_live()) return;
    $db=ij_db();$db->exec(file_get_contents(__DIR__.'/../database/slugs.sql'));
    // Asignación inicial sin tocar texto, fechas ni estado de publicaciones existentes.
    $rows=$db->query('SELECT p.id,p.title FROM posts p LEFT JOIN post_urls u ON u.post_id=p.id WHERE u.post_id IS NULL ORDER BY p.id')->fetchAll();
    foreach ($rows as $row) {
        $db->beginTransaction();
        try { $s=$db->prepare('SELECT slug FROM post_urls WHERE post_id=? FOR UPDATE');$s->execute([$row['id']]);if (!$s->fetchColumn()) ij_assign_slug((int)$row['id'],$row['title']);$db->commit(); }
        catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack();throw $e; }
    }
    $ready=true;
}
function ij_public_url(string $type,string $slug): string { return ($type==='opinion' ? '/opinion/' : '/noticias/').$slug; }
