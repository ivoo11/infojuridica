<?php require_once __DIR__ . '/../app/content.php'; $homeOpinions = array_slice(ij_posts('opinion'),0,3); if (!$homeOpinions) return; ?>
<section class="section rulings-section" id="opinion">
    <div class="container">
        <div class="section-header">
            <div><div class="section-kicker">Voces de la abogacía</div><h2 class="section-title">Opinión</h2></div>
        </div>
        <div class="rulings-grid">
            <?php foreach ($homeOpinions as $column): ?>
            <article class="ruling">
                <a class="opinion-card" href="<?= ij_e($column['url']) ?>">
                    <div class="opinion-author">
                        <img src="<?= ij_e($column['author_photo'] ?? '/assets/img/autor-placeholder.svg') ?: '/assets/img/autor-placeholder.svg' ?>" alt="Retrato pendiente del autor" width="72" height="88">
                        <div><strong><?= ij_e($column['author_name'] ?? 'Autor de demostración') ?></strong><span><?= ij_e($column['author_role'] ?? 'Nombre y cargo por incorporar') ?></span></div>
                    </div>
                    <span class="category">OPINIÓN<?= ij_live() ? '' : ' · DEMOSTRACIÓN' ?></span>
                    <h3><?= htmlspecialchars($column['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($column['bajada'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
