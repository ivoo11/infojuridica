<?php
$homeSettings = require __DIR__ . '/../app/home-settings.php';
$banner = $homeSettings['publicidad'];
if (is_array($banner) && !empty($banner['imagen']) && !empty($banner['url'])):
?>
<aside class="container ad-banner" aria-label="Publicidad">
    <small>PUBLICIDAD</small>
    <a href="<?= htmlspecialchars($banner['url'], ENT_QUOTES, 'UTF-8') ?>" rel="sponsored noopener"><img src="<?= htmlspecialchars($banner['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($banner['nombre'] ?? 'Publicidad', ENT_QUOTES, 'UTF-8') ?>"></a>
</aside>
<?php endif; ?>
