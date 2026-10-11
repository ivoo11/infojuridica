<?php
require __DIR__.'/../app/admin.php'; $user=ij_require_user();
require __DIR__.'/../app/newsletter.php';
$edition=ij_newsletter((int)($_GET['id'] ?? 0));
if (!$edition) { http_response_code(404);exit('La edición no existe.'); }
$posts=ij_newsletter_posts($edition);
$events=$edition['include_agenda'] ? array_slice(ij_upcoming_events(),0,3) : [];
require __DIR__.'/../includes/newsletter-template.php';
