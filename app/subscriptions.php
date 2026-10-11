<?php
require_once __DIR__.'/bootstrap.php';
function ij_subscriptions_ready(): void {
    static $ready=false;
    if (!$ready) { ij_db()->exec(file_get_contents(__DIR__.'/../database/newsletter-subscribers.sql'));$ready=true; }
}
function ij_subscription_consent(): string {
    return 'Quiero recibir el newsletter de INFO JURÍDICA por correo electrónico. Puedo darme de baja cuando quiera.';
}
function ij_subscription_email(array $input): string {
    if (($input['consent'] ?? '')!=='1') throw new InvalidArgumentException('Marcá la casilla si querés recibir el newsletter.');
    $email=$input['email'] ?? '';
    if (!is_string($email)) throw new InvalidArgumentException('Ingresá un correo válido.');
    $email=mb_strtolower(trim($email));
    if (strlen($email)>190 || !filter_var($email,FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Ingresá un correo válido.');
    return $email;
}
function ij_subscription_request(string $email, string $remote): void {
    ij_subscriptions_ready();$db=ij_db();$db->beginTransaction();
    try {
        // Only a digest is retained for rate limiting, never the raw address.
        $key=hash('sha256',gmdate('Y-m-d-H').'|'.$remote);
        $db->exec('DELETE FROM newsletter_request_limits WHERE window_start < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 2 HOUR)');
        $s=$db->prepare('INSERT INTO newsletter_request_limits(key_hash,attempts) VALUES(?,0) ON DUPLICATE KEY UPDATE key_hash=key_hash');$s->execute([$key]);
        $s=$db->prepare('SELECT attempts FROM newsletter_request_limits WHERE key_hash=? FOR UPDATE');$s->execute([$key]);
        if ((int)$s->fetchColumn()>=5) throw new InvalidArgumentException('Recibimos varias solicitudes. Intentá nuevamente más tarde.');
        $s=$db->prepare('UPDATE newsletter_request_limits SET attempts=attempts+1 WHERE key_hash=?');$s->execute([$key]);
        $s=$db->prepare('INSERT INTO newsletter_subscribers(email) VALUES(?) ON DUPLICATE KEY UPDATE email=email');$s->execute([$email]);
        $s=$db->prepare('SELECT id,status FROM newsletter_subscribers WHERE email=? FOR UPDATE');$s->execute([$email]);$subscriber=$s->fetch();
        if ($subscriber['status']!=='active') {
            $s=$db->prepare("UPDATE newsletter_subscribers SET status='pending',requested_at=UTC_TIMESTAMP(),confirmed_at=NULL WHERE id=?");$s->execute([$subscriber['id']]);
            $s=$db->prepare('INSERT INTO newsletter_consents(subscriber_id,consent_version,consent_text,source) VALUES(?,?,?,?)');
            $s->execute([$subscriber['id'],'2026-10-11',ij_subscription_consent(),'portal-form']);
        }
        $db->commit();
    } catch (Throwable $e) { if ($db->inTransaction()) $db->rollBack();throw $e; }
}
