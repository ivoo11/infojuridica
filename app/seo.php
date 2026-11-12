<?php
declare(strict_types=1);
require_once __DIR__.'/bootstrap.php';
const IJ_SEO_ORIGIN='https://infojuridica.com.ar';
function ij_seo_url(string $path): string {
    if(str_starts_with($path,'/') && !str_starts_with($path,'//'))return IJ_SEO_ORIGIN.$path;
    return ij_link_url($path)?$path:IJ_SEO_ORIGIN.'/';
}
function ij_seo_page(string $path,int $page=1): string {
    return ij_seo_url($path.($page>1?'?pagina='.$page:''));
}
function ij_seo_date(?string $value): ?string {
    if(!$value)return null;
    try{return (new DateTimeImmutable($value,new DateTimeZone('UTC')))->format(DateTimeInterface::ATOM);}catch(Throwable $e){return null;}
}
function ij_seo_organization(): array {
    return ['@type'=>'Organization','@id'=>IJ_SEO_ORIGIN.'/#organization','name'=>'INFO JURÍDICA','url'=>IJ_SEO_ORIGIN.'/','logo'=>['@type'=>'ImageObject','url'=>IJ_SEO_ORIGIN.'/assets/img/logo-infojuridica.png'],'sameAs'=>['https://www.instagram.com/infojuridicaarg/']];
}
function ij_seo_article(array $post): array {
    $url=ij_seo_url($post['url']);$opinion=($post['type'] ?? '')==='opinion';
    $article=['@type'=>$opinion?'Article':'NewsArticle','@id'=>$url.'#article','mainEntityOfPage'=>['@type'=>'WebPage','@id'=>$url],'url'=>$url,'headline'=>$post['titulo'],'description'=>$post['bajada'],'inLanguage'=>'es-AR','articleSection'=>$opinion?'Opinión':($post['category_name'] ?? $post['categoria']),'publisher'=>ij_seo_organization()];
    $published=ij_seo_date($post['published_at'] ?? null);$modified=ij_seo_date($post['updated_at'] ?? null);
    if($published)$article['datePublished']=$published;
    if($modified)$article['dateModified']=$published && $published>$modified?$published:$modified;
    $image=$opinion?($post['author_photo'] ?? ''):($post['image'] ?? '');
    if($image && !str_ends_with(strtolower($image),'.svg'))$article['image']=[ij_seo_url($image)];
    $article['author']=!empty($post['author_name'])?['@type'=>'Person','name'=>$post['author_name']]:['@type'=>'Organization','@id'=>IJ_SEO_ORIGIN.'/#organization','name'=>'Redacción INFO JURÍDICA'];
    $category=$opinion?'/opinion/':'/'.($post['category_slug'] ?? 'noticias').'/';
    $breadcrumb=['@type'=>'BreadcrumbList','itemListElement'=>[
        ['@type'=>'ListItem','position'=>1,'name'=>'Inicio','item'=>IJ_SEO_ORIGIN.'/'],
        ['@type'=>'ListItem','position'=>2,'name'=>$opinion?'Opinión':($post['category_name'] ?? $post['categoria']),'item'=>ij_seo_url($category)],
        ['@type'=>'ListItem','position'=>3,'name'=>$post['titulo'],'item'=>$url]
    ]];
    return ['@context'=>'https://schema.org','@graph'=>[$article,$breadcrumb]];
}
function ij_seo_json(array $data): string {
    return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_THROW_ON_ERROR);
}
