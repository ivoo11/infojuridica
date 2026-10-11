<?php
require __DIR__.'/../app/admin.php';$user=ij_require_user();require __DIR__.'/../app/subscriptions.php';ij_subscriptions_ready();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
ij_check_csrf();$id=(int)ij_field('id');
$s=ij_db()->prepare("UPDATE newsletter_subscribers SET status='active' WHERE id=? AND status='pending' AND EXISTS(SELECT 1 FROM newsletter_consents WHERE subscriber_id=newsletter_subscribers.id)");$s->execute([$id]);
header('Location: /admin/newsletter-suscriptores.php');
