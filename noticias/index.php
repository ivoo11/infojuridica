<?php
require_once __DIR__.'/../app/content.php';
$listingTitle='Últimas noticias';$listingDescription='Todas las noticias publicadas, de más reciente a más antigua.';
$listingItems=ij_posts('news');$listingBase='/noticias/';require __DIR__.'/../includes/listing.php';
