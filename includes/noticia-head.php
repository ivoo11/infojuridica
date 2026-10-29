<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($noticia["titulo"], ENT_QUOTES, "UTF-8") ?> — Info Jurídica</title>

    <meta
        name="description"
        content="<?= htmlspecialchars($noticia["bajada"], ENT_QUOTES, "UTF-8") ?>"
    >

    <?php require __DIR__ . '/favicon.php'; ?>
    <?php require __DIR__ . '/social-article.php'; ?>

    <!-- TIPOGRAFÍAS -->
    <?php if (ij_live() && empty($isPreview) && empty($isUnlisted)): ?><link rel="canonical" href="<?= ij_e('https://infojuridica.com.ar'.$noticia['url']) ?>"><?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600;8..60,700&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet"
      href="/assets/css/styles.css?v=<?= filemtime(__DIR__ . '/../assets/css/styles.css') ?>">

<?php if (!ij_live() || !empty($isPreview) || !empty($isUnlisted)): ?><meta name="robots" content="noindex, nofollow"><?php endif; ?>
<link rel="stylesheet" href="/assets/css/navigation.css?v=<?= filemtime(__DIR__ . '/../assets/css/navigation.css') ?>">
<link rel="stylesheet" href="/assets/css/noticia.css?v=<?= filemtime(__DIR__ . '/../assets/css/noticia.css') ?>">
</head>

<body>
