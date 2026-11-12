<?php
declare(strict_types=1);
require_once __DIR__.'/app/seo.php';
header('Cache-Control: no-cache, max-age=0');header('X-Content-Type-Options: nosniff');
if(!ij_live()){http_response_code(503);header('Content-Type: text/plain; charset=utf-8');exit('Sitemap no disponible.');}
try {
    require_once __DIR__.'/app/content.php';require_once __DIR__.'/app/events.php';ij_events_ready();
    $entries=[];foreach(['/','/noticias/','/opinion/','/justicia/','/profesion/','/colegiatura/','/universidades/','/agenda/','/padron/'] as $path)$entries[]=['url'=>ij_seo_url($path)];
    foreach(ij_posts() as $post)$entries[]=['url'=>ij_seo_url($post['url']),'lastmod'=>ij_seo_date($post['updated_at'] ?? $post['published_at'])];
    foreach(ij_db()->query("SELECT id,updated_at FROM events WHERE status='published'") as $event)$entries[]=['url'=>ij_seo_url('/agenda/actividad.php?id='.(int)$event['id']),'lastmod'=>ij_seo_date($event['updated_at'])];
    // Split automatically if the archive ever exceeds one sitemap's URL limit.
    $chunk=40000;$count=count($entries);$page=filter_var($_GET['pagina'] ?? 0,FILTER_VALIDATE_INT);
    if($page===false || $page<0 || ($page && $page>(int)ceil($count/$chunk))){http_response_code(404);header('Content-Type: text/plain; charset=utf-8');exit('Sitemap no encontrado.');}
    header('Content-Type: application/xml; charset=utf-8');
    $xml=new XMLWriter();$xml->openMemory();$xml->startDocument('1.0','UTF-8');
    if(!$page && $count>$chunk){$xml->startElement('sitemapindex');$xml->writeAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');for($i=1;$i<=(int)ceil($count/$chunk);$i++){$xml->startElement('sitemap');$xml->writeElement('loc',ij_seo_url('/sitemap.php?pagina='.$i));$xml->endElement();}}
    else {$xml->startElement('urlset');$xml->writeAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');foreach($page?array_slice($entries,($page-1)*$chunk,$chunk):$entries as $entry){$xml->startElement('url');$xml->writeElement('loc',$entry['url']);if(!empty($entry['lastmod']))$xml->writeElement('lastmod',$entry['lastmod']);$xml->endElement();}}
    $xml->endElement();$xml->endDocument();echo $xml->outputMemory();
} catch(Throwable $e){http_response_code(503);header('Content-Type: text/plain; charset=utf-8');echo 'Sitemap temporalmente no disponible.';}
