<?php
$listingCount = count($listingItems);
$listingPages = max(1, (int) ceil($listingCount / 6));
$requestedPage = filter_var($_GET['pagina'] ?? 1, FILTER_VALIDATE_INT);
$listingPage = min($listingPages, max(1, $requestedPage ?: 1));
$listingSlice = array_slice($listingItems, ($listingPage - 1) * 6, 6);
$noticia = ['titulo' => $listingTitle, 'bajada' => $listingDescription];
require __DIR__ . '/listing-head.php';
require __DIR__ . '/header.php';
require __DIR__ . '/navbar.php';
?>
<main class="container listing-page">
    <header class="listing-heading<?= !empty($listingIsCategory) ? ' listing-heading-category' : '' ?>"><h1><?= htmlspecialchars($listingTitle, ENT_QUOTES, 'UTF-8') ?></h1><?php if (empty($listingIsCategory)): ?><p><?= htmlspecialchars($listingDescription, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?><?php if (!ij_live()): ?><p>Vista de demostración · Contenido de la maqueta</p><?php endif; ?></header>
    <div class="news-grid">
        <?php foreach ($listingSlice as $item): ?>
        <?php require __DIR__ . '/post-card.php'; ?>
        <?php endforeach; ?>
    </div>
    <?php if (!$listingCount): ?><p><?= !empty($listingIsCategory) ? 'Todavía no hay noticias en esta sección.' : 'No encontramos publicaciones para esta búsqueda.' ?></p><?php endif; ?>
    <?php if ($listingPages > 1): ?>
    <nav class="listing-pagination" aria-label="Páginas de resultados">
        <?php for ($i = 1; $i <= $listingPages; $i++): ?>
            <?php if ($i === $listingPage): ?><span aria-current="page"><?= $i ?></span>
            <?php else: ?><a href="<?= htmlspecialchars($listingBase . (str_contains($listingBase, '?') ? '&' : '?') . 'pagina=' . $i, ENT_QUOTES, 'UTF-8') ?>"><?= $i ?></a><?php endif; ?>
        <?php endfor; ?>
    </nav>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/footer.php'; ?>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../assets/js/app.js') ?>"></script>
<script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__ . '/../assets/js/navigation.js') ?>"></script>
</body></html>
