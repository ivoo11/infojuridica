<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/slugs.php';
ij_slugs_ready();
function ij_post_map(array $row): array {
    $row['titulo'] = $row['title']; $row['bajada'] = $row['summary'];
    $row['categoria'] = $row['type'] === 'opinion' ? 'OPINIÓN' : mb_strtoupper($row['category_name']);
    $row['imagen'] = $row['image'] ?: '/assets/img/favicon.png';
    $row['fecha'] = $row['published_at'] ?: $row['created_at'];
    $row['url'] = ij_public_url($row['type'],$row['slug']);
    return $row;
}
function ij_select(): string { return 'SELECT p.*, u.slug, c.name category_name, c.slug category_slug, a.name author_name, a.role author_role, a.photo author_photo FROM posts p LEFT JOIN post_urls u ON u.post_id=p.id JOIN categories c ON c.id=p.category_id LEFT JOIN authors a ON a.id=p.author_id'; }
function ij_posts(?string $type = null): array {
    if (!ij_live()) {
        $news = require __DIR__ . '/demo-content.php'; $opinion = require __DIR__ . '/demo-opinions.php';
        foreach ($news as &$p) { $p['type']='news'; $p['url']='/noticia.php?id='.$p['id']; } unset($p);
        foreach ($opinion as &$p) { $p['type']='opinion'; $p['imagen']='/assets/img/autor-placeholder.svg'; $p['url']='/opinion.php?id='.$p['id']; } unset($p);
        return $type === 'news' ? $news : ($type === 'opinion' ? $opinion : array_merge($news,$opinion));
    }
    $sql = ij_select() . " WHERE p.status='published' AND p.published_at <= UTC_TIMESTAMP()";
    $args = [];
    if ($type) { $sql .= ' AND p.type=?'; $args[]=$type; }
    $stmt=ij_db()->prepare($sql . ' ORDER BY p.published_at DESC,p.id DESC'); $stmt->execute($args);
    return array_map('ij_post_map',$stmt->fetchAll());
}
function ij_post(int $id, string $type, bool $preview = false): ?array {
    if (!ij_live()) { foreach (ij_posts($type) as $p) if ($p['id']===$id) return $p; return null; }
    $sql=ij_select().' WHERE p.id=? AND p.type=?';
    if (!$preview) $sql .= " AND (p.status='unlisted' OR (p.status='published' AND p.published_at <= UTC_TIMESTAMP()))";
    $stmt=ij_db()->prepare($sql);$stmt->execute([$id,$type]);$row=$stmt->fetch();
    return $row ? ij_post_map($row) : null;
}

function ij_post_by_slug(string $slug,string $type): ?array {
    if (!ij_live() || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D',$slug) || strlen($slug)>180) return null;
    $id=ij_slug_owner($slug);
    return $id===null ? null : ij_post($id,$type);
}
