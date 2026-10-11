<?php
require_once __DIR__ . '/app/content.php';
$searchTerm = is_string($_GET['q'] ?? null) ? mb_substr(trim($_GET['q']), 0, 200) : '';
$allArticles = ij_posts('news');
foreach ($allArticles as &$item) if (!ij_live()) $item['url'] = '/noticia.php?id=' . $item['id'];
unset($item);
$opinions = ij_posts('opinion');
foreach ($opinions as &$item) { if (!ij_live()) $item['url'] = '/opinion.php?id=' . $item['id']; $item['imagen'] = ij_live() ? (($item['author_photo'] ?? '') ?: '/assets/img/autor-placeholder.svg') : '/assets/img/autor-placeholder.svg'; }
unset($item);
function normalizeSearch(string $value): string { return strtr(mb_strtolower($value), ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u']); }
$listingItems = array_values(array_filter(array_merge($allArticles, $opinions), fn($item) => $searchTerm === '' || str_contains(normalizeSearch($item['titulo'] . ' ' . $item['bajada'] . ' ' . $item['categoria']), normalizeSearch($searchTerm))));
$listingTitle = 'Resultados de búsqueda';
$listingDescription = $searchTerm === '' ? (ij_live() ? 'Todas las publicaciones.' : 'Todas las publicaciones de demostración.') : 'Resultados para: ' . $searchTerm;
$listingBase = '/buscar/?q=' . rawurlencode($searchTerm);
require __DIR__ . '/includes/listing.php';
