<?php
require_once __DIR__.'/content.php';
require_once __DIR__.'/events.php';
function ij_newsletters_ready(): void {
    static $ready=false;
    if (!$ready) { ij_db()->exec(file_get_contents(__DIR__.'/../database/newsletters.sql')); $ready=true; }
}
function ij_newsletter(int $id): ?array {
    ij_newsletters_ready();
    $s=ij_db()->prepare('SELECT * FROM newsletters WHERE id=?'); $s->execute([$id]);
    $row=$s->fetch();
    if (!$row) return null;
    $row['ids']=json_decode($row['post_ids'],true,512,JSON_THROW_ON_ERROR);
    return $row;
}
function ij_newsletter_values(array $input, array $available): array {
    $subject=$input['subject'] ?? ''; $intro=$input['introduction'] ?? ''; $ids=$input['posts'] ?? [];
    if (!is_string($subject) || !is_string($intro) || !is_array($ids)) throw new InvalidArgumentException('Revisá los campos de la edición.');
    $subject=trim($subject); $intro=trim($intro);
    if ($subject==='' || mb_strlen($subject)>190 || preg_match('/[\r\n]/',$subject)) throw new InvalidArgumentException('Completá un asunto de hasta 190 caracteres, en una sola línea.');
    if (mb_strlen($intro)>3000) throw new InvalidArgumentException('La introducción puede tener hasta 3000 caracteres.');
    if (count($ids)>6) throw new InvalidArgumentException('Elegí una principal y hasta cinco noticias más.');
    $allowed=array_column($available,'id'); $chosen=[];
    foreach ($ids as $id) {
        if (!is_string($id) || !preg_match('/^\d+$/D',$id)) throw new InvalidArgumentException('Elegí una publicación válida.');
        if ($id==='0') continue;
        $id=(int)$id;
        if (!in_array($id,array_map('intval',$allowed),true)) throw new InvalidArgumentException('Una noticia ya no está publicada. Reemplazala antes de guardar.');
        if (in_array($id,$chosen,true)) throw new InvalidArgumentException('Una noticia está repetida. Elegí otra o dejá ese lugar vacío.');
        $chosen[]=$id;
    }
    if (!$chosen || empty($ids[0]) || $ids[0]==='0') throw new InvalidArgumentException('Elegí la noticia principal.');
    return ['subject'=>$subject,'introduction'=>$intro,'ids'=>$chosen,'include_agenda'=>isset($input['include_agenda']) ? 1 : 0];
}
function ij_newsletter_posts(array $edition): array {
    $posts=[];
    foreach (ij_posts('news') as $post) $posts[(int)$post['id']]=$post;
    $selected=[];
    foreach ($edition['ids'] as $id) if (isset($posts[(int)$id])) $selected[]=$posts[(int)$id];
    return $selected;
}
function ij_newsletter_url(string $path): string {
    if (preg_match('~^https?://~i',$path)) return $path;
    return 'https://infojuridica.com.ar/'.ltrim($path,'/');
}
