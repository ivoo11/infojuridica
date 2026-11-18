<?php require_once __DIR__.'/../app/pagination.php';if($paginationTotal>1):?>
<nav class="listing-pagination" aria-label="Páginas de resultados">
<?php if($paginationCurrent>1):?><a class="pagination-previous" rel="prev" href="<?= ij_e(ij_pagination_url($paginationBase,$paginationCurrent-1)) ?>">Anterior</a><?php else:?><span class="pagination-previous" aria-disabled="true">Anterior</span><?php endif;?>
<div class="pagination-pages">
<?php foreach(ij_pagination_items($paginationCurrent,$paginationTotal) as $number):?>
<?php if($number===null):?><span class="pagination-ellipsis" aria-hidden="true">…</span>
<?php elseif($number===$paginationCurrent):?><span class="pagination-number" aria-current="page" aria-label="Página <?= $number ?>, actual"><?= $number ?></span>
<?php else:?><a class="pagination-number" aria-label="Ir a la página <?= $number ?>" href="<?= ij_e(ij_pagination_url($paginationBase,$number)) ?>"><?= $number ?></a><?php endif;?>
<?php endforeach;?></div>
<span class="pagination-summary" aria-label="Página <?= (int)$paginationCurrent ?> de <?= (int)$paginationTotal ?>"><?= (int)$paginationCurrent ?> <span aria-hidden="true">/</span> <?= (int)$paginationTotal ?></span>
<?php if($paginationCurrent<$paginationTotal):?><a class="pagination-next" rel="next" href="<?= ij_e(ij_pagination_url($paginationBase,$paginationCurrent+1)) ?>">Siguiente</a><?php else:?><span class="pagination-next" aria-disabled="true">Siguiente</span><?php endif;?>
</nav>
<?php endif;?>
