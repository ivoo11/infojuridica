<?php
require_once __DIR__ . '/app/content.php';
if (!isset($noticia)) {
    $articleId = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
    $noticia = $articleId === false ? null : ij_post($articleId, 'news');
    if (!$noticia) { http_response_code(404); echo 'Noticia no encontrada.'; return; }
}
$isOpinion = $isOpinion ?? false;
if (ij_live() && empty($isPreview) && isset($_SERVER['REQUEST_URI'])) {
    $path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
    if ($path!==$noticia['url']) { header('Location: '.$noticia['url'],true,301);exit; }
}

require __DIR__ . '/includes/noticia-head.php';
require __DIR__ . '/includes/header.php';

?>
<main class="article-page container">
    <?php if (!empty($isPreview)): ?><p class="meta">VISTA PREVIA PRIVADA · Versión guardada · <a href="/admin/editar.php?id=<?= (int)$noticia['id'] ?>">Volver al editor</a></p><?php endif; ?>
    <nav class="article-breadcrumb" aria-label="Ubicación"><a href="/">Inicio</a> / <?= htmlspecialchars($noticia['categoria'], ENT_QUOTES, 'UTF-8') ?></nav>
    <article>
        <header class="article-heading">
            <?php if (!ij_live()): ?><p class="meta">VISTA DE DEMOSTRACIÓN</p><?php endif; ?>
            <h1><?= htmlspecialchars($noticia['titulo'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="article-summary"><?= htmlspecialchars($noticia['bajada'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php if ($isOpinion): ?>
            <div class="article-author"><img src="<?= ij_e(($noticia['author_photo'] ?? '') ?: '/assets/img/autor-placeholder.svg') ?>" alt="<?= ij_e('Retrato de '.($noticia['author_name'] ?? 'autor')) ?>" width="72" height="72"><div><strong><?= ij_e($noticia['author_name'] ?? 'Autor de demostración') ?></strong><?php if (!empty($noticia['author_role'])): ?><p><?= ij_e($noticia['author_role']) ?></p><?php endif; ?><p class="meta"><time datetime="<?= ij_e($noticia['fecha']) ?>"><?= ij_date($noticia['fecha']) ?></time></p></div></div>
            <?php endif; ?>
            <?php if (!$isOpinion): ?><p class="meta">Por <?= ij_e($noticia['author_name'] ?? ($isOpinion ? 'Autor de demostración' : 'Redacción INFO JURÍDICA')) ?> · <time datetime="<?= ij_e($noticia['fecha']) ?>"><?= ij_date($noticia['fecha']) ?></time></p><?php endif; ?>
        </header>
        <?php if (!$isOpinion): ?>
        <figure class="article-image">
            <img src="<?= htmlspecialchars($noticia['imagen'] ?? 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?auto=format&fit=crop&w=1600&q=85', ENT_QUOTES, 'UTF-8') ?>" alt="<?= ij_e($noticia['image_alt'] ?? 'Imagen ilustrativa de la noticia') ?>" width="1600" height="900">
            <?php if (!ij_live() || !empty($noticia['image_caption'])): ?><figcaption><?= ij_e($noticia['image_caption'] ?? 'Imagen ilustrativa · Unsplash.') ?></figcaption><?php endif; ?>
        </figure>
        <?php endif; ?>
        <div class="article-body">
            <?php if (ij_live()): ?>
                <?php if ($isOpinion): ?><p class="meta">Las columnas expresan la opinión de sus autores.</p><?php endif; ?>
                <?= ij_body($noticia['body']) ?>
            <?php else: ?>
            <?php if ($isOpinion): ?><p class="meta">Las columnas expresan la opinión de sus autores. Esta página contiene únicamente contenido de demostración.</p><?php endif; ?>
            <p><strong>Esta es una muestra de la plantilla de noticia.</strong> El título y la bajada provienen de la portada; este cuerpo sirve exclusivamente para revisar la lectura, la tipografía y los espacios antes de incorporar contenido editorial.</p>
            <p>La página reúne los elementos de una nota: categoría, título, bajada, autor, fecha, imagen y opciones para compartir. Su composición se adapta al ancho de pantalla y mantiene la identidad visual de INFO JURÍDICA.</p>
            <h2>Información con contexto</h2>
            <p>Los subtítulos organizan la lectura y permiten recorrer una noticia extensa. Los párrafos conservan un ancho cómodo tanto en una computadora como en un teléfono.</p>
            <blockquote>Un espacio para destacar una cita o una idea central de la noticia.</blockquote>
            <p>El contenido definitivo podrá incorporar enlaces a fuentes y documentos. La consulta electoral mantiene su acceso independiente desde <a href="/padron/">el padrón</a>.</p>
            <?php endif; ?>
        </div>
        <?php require __DIR__ . '/includes/share-article.php'; ?>
        <div class="article-return"><a class="article-back" href="/">Volver a la portada</a></div>
    </article>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/assets/js/app.js') ?>"></script>
<script src="/assets/js/noticia.js?v=<?= filemtime(__DIR__ . '/assets/js/noticia.js') ?>"></script>
<script src="/assets/js/navigation.js?v=<?= filemtime(__DIR__ . '/assets/js/navigation.js') ?>"></script>
</body>
</html>
