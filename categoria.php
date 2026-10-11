<?php
require_once __DIR__ . '/app/content.php';
$categoryNames = ['justicia' => 'Justicia', 'profesion' => 'Profesión', 'colegiatura' => 'Colegiatura', 'universidades' => 'Universidades'];
$categorySlug = $categorySlug ?? (is_string($_GET['categoria'] ?? null) ? $_GET['categoria'] : '');
if (!isset($categoryNames[$categorySlug])) { http_response_code(404); echo 'Categoría no encontrada.'; return; }
$listingTitle = $categoryNames[$categorySlug];
$listingDescription = 'Noticias de ' . $listingTitle . ' en INFO JURÍDICA.';
$listingIsCategory = true;
$allArticles = ij_posts('news');
$listingItems = array_values(array_filter($allArticles, fn($item) => mb_strtolower($item['categoria']) === mb_strtolower($listingTitle)));
foreach ($listingItems as &$item) if (!ij_live()) $item['url'] = '/noticia.php?id=' . $item['id'];
unset($item);
$listingBase = '/' . $categorySlug . '/';
require __DIR__ . '/includes/listing.php';
