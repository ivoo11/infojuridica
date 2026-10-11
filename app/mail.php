<?php
require_once __DIR__.'/newsletter.php';
require_once __DIR__.'/subscriptions.php';
require_once __DIR__.'/../vendor/autoload.php';
function ij_mail_config(): array {
    $path=getenv('INFOJURIDICA_MAIL_CONFIG') ?: __DIR__.'/../config/mail.local.php';
    return is_file($path) ? require $path : require __DIR__.'/../config/mail.example.php';
}
function ij_mail_enabled(): bool {
    $c=ij_mail_config();return !empty($c['enabled']) && !empty($c['smtp_password']);
}
function ij_mail_ready(): void {
    static $ready=false;
    if (!$ready) { ij_newsletters_ready();ij_subscriptions_ready();ij_db()->exec(file_get_contents(__DIR__.'/../database/newsletter-mail.sql'));$ready=true; }
}
function ij_mail_message(string $recipient,array $message): PHPMailer\PHPMailer\PHPMailer {
    $c=ij_mail_config();$m=new PHPMailer\PHPMailer\PHPMailer(true);
    if (!filter_var($recipient,FILTER_VALIDATE_EMAIL) || strlen($recipient)>190) throw new InvalidArgumentException('Revisá el correo de destino.');
    if (!in_array((int)$c['smtp_port'],[465,587],true)) throw new InvalidArgumentException('Usá el puerto SMTP 465 o 587.');
    $m->isSMTP();$m->Host=$c['smtp_host'];$m->Port=(int)$c['smtp_port'];$m->SMTPAuth=true;
    $m->SMTPSecure=$m->Port===465 ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $m->Username=$c['smtp_user'];$m->Password=$c['smtp_password'];$m->Timeout=20;$m->getSMTPInstance()->Timelimit=25;
    $m->SMTPDebug=0;$m->CharSet='UTF-8';$m->setFrom($c['from'],'INFO JURÍDICA');$m->addAddress($recipient);
    $m->isHTML(true);$m->Subject=$message['subject'];$m->Body=$message['html'];
    $m->AltBody=html_entity_decode(strip_tags(preg_replace('~</(?:p|h[1-6]|tr)>|<br\s*/?>~i',"\n",$message['html'])),ENT_QUOTES|ENT_HTML5,'UTF-8');
    foreach ($message['headers'] ?? [] as $key=>$value) $m->addCustomHeader($key,$value);
    return $m;
}
function ij_mail_snapshot(int $editionId): array {
    $edition=ij_newsletter($editionId);
    if (!$edition) throw new InvalidArgumentException('La edición no existe.');
    $posts=ij_newsletter_posts($edition);
    if (!$posts || count($posts)!==count($edition['ids'])) throw new InvalidArgumentException('Revisá las publicaciones seleccionadas antes de enviar.');
    $edition['send_date']=ij_now();
    $events=$edition['include_agenda'] ? array_slice(ij_upcoming_events(),0,3) : [];
    foreach ($events as &$event) $event['newsletter_dates']=ij_event_card_schedule(ij_event_days($event))['dates'];
    unset($event);
    return ['edition'=>$edition,'posts'=>$posts,'events'=>$events];
}
function ij_mail_render(array $snapshot,?int $subscriberId=null): array {
    $edition=$snapshot['edition'];$posts=$snapshot['posts'];$events=$snapshot['events'];$newsletterIsEmail=true;
    $unsubscribeUrl=$subscriberId ? ij_subscription_unsubscribe_url($subscriberId) : '';
    ob_start();
    try {require __DIR__.'/../includes/newsletter-template.php';$html=ob_get_clean();}catch(Throwable $e){ob_end_clean();throw $e;}
    return ['subject'=>$edition['subject'],'html'=>$html,'headers'=>$unsubscribeUrl ? ['List-Unsubscribe'=>'<'.$unsubscribeUrl.'>','List-Unsubscribe-Post'=>'List-Unsubscribe=One-Click'] : []];
}
function ij_mail_queue_test(int $editionId,string $recipient): void {
    if (!ij_mail_enabled()) throw new InvalidArgumentException('Primero conectá la casilla SMTP en config/mail.local.php.');
    if (!filter_var($recipient,FILTER_VALIDATE_EMAIL) || strlen($recipient)>190) throw new InvalidArgumentException('Ingresá un correo válido para la prueba.');
    ij_mail_ready();$message=ij_mail_render(ij_mail_snapshot($editionId));
    $s=ij_db()->prepare("INSERT INTO newsletter_mail_jobs(kind,recipient,subject,html) VALUES('test',?,?,?)");$s->execute([$recipient,mb_substr('[PRUEBA] '.$message['subject'],0,190),$message['html']]);
}
function ij_mail_queue_campaign(int $editionId): int {
    if (!ij_mail_enabled()) throw new InvalidArgumentException('Primero conectá la casilla SMTP.');
    ij_mail_ready();$snapshot=ij_mail_snapshot($editionId);$db=ij_db();$db->beginTransaction();
    try {
        $s=$db->prepare('SELECT id FROM newsletters WHERE id=? FOR UPDATE');$s->execute([$editionId]);
        $s=$db->prepare('SELECT id FROM newsletter_campaigns WHERE edition_id=?');$s->execute([$editionId]);
        if ($s->fetchColumn()) throw new InvalidArgumentException('Esta edición ya fue preparada para enviar. No se enviará otra vez.');
        $subscribers=$db->query("SELECT id,email FROM newsletter_subscribers WHERE status='active' ORDER BY id")->fetchAll();
        if (!$subscribers) throw new InvalidArgumentException('Todavía no hay suscriptores activos.');
        $s=$db->prepare('INSERT INTO newsletter_campaigns(edition_id,snapshot) VALUES(?,?)');$s->execute([$editionId,json_encode($snapshot,JSON_THROW_ON_ERROR)]);$campaign=(int)$db->lastInsertId();
        $s=$db->prepare("INSERT INTO newsletter_mail_jobs(campaign_id,subscriber_id,kind,recipient) VALUES(?,?,'newsletter',?)");
        foreach ($subscribers as $subscriber) $s->execute([$campaign,$subscriber['id'],$subscriber['email']]);
        $db->commit();return count($subscribers);
    }catch(Throwable $e){if($db->inTransaction())$db->rollBack();throw $e;}
}
function ij_mail_process(?callable $transport=null): string {
    if (!ij_mail_enabled()) return 'disabled';
    ij_mail_ready();$db=ij_db();$lock='ij_mail_'.substr(hash('sha256',ij_config()['db']['name']),0,24);
    $s=$db->prepare('SELECT GET_LOCK(?,0)');$s->execute([$lock]);if(!(int)$s->fetchColumn())return 'busy';
    try {
        // Reserve before networking. A crash leaves processing for review, never an automatic duplicate.
        $limit=max(1,min(90,(int)(ij_mail_config()['daily_limit'] ?? 90)));
        if ((int)$db->query('SELECT COUNT(*) FROM newsletter_mail_jobs WHERE reserved_at >= DATE_SUB(UTC_TIMESTAMP(),INTERVAL 24 HOUR)')->fetchColumn()>=$limit) return 'daily_limit';
        if ((int)$db->query('SELECT COUNT(*) FROM newsletter_mail_jobs WHERE reserved_at > DATE_SUB(UTC_TIMESTAMP(),INTERVAL 60 SECOND)')->fetchColumn()) return 'minute_limit';
        $job=$db->query("SELECT * FROM newsletter_mail_jobs WHERE status='queued' ORDER BY id LIMIT 1")->fetch();if(!$job)return 'empty';
        if ($job['kind']==='newsletter') {
            $s=$db->prepare('SELECT status FROM newsletter_subscribers WHERE id=?');$s->execute([$job['subscriber_id']]);
            if ($s->fetchColumn()!=='active') {$s=$db->prepare("UPDATE newsletter_mail_jobs SET status='cancelled' WHERE id=?");$s->execute([$job['id']]);return 'cancelled';}
        }
        $s=$db->prepare("UPDATE newsletter_mail_jobs SET status='processing',reserved_at=UTC_TIMESTAMP() WHERE id=?");$s->execute([$job['id']]);
        try {
            if($job['kind']==='newsletter'){$s=$db->prepare('SELECT snapshot FROM newsletter_campaigns WHERE id=?');$s->execute([$job['campaign_id']]);$message=ij_mail_render(json_decode($s->fetchColumn(),true,512,JSON_THROW_ON_ERROR),(int)$job['subscriber_id']);}
            else $message=['subject'=>$job['subject'],'html'=>$job['html'],'headers'=>[]];
            // Recheck immediately before transport; queued recipients may have unsubscribed.
            if($job['subscriber_id']){$s=$db->prepare('SELECT status FROM newsletter_subscribers WHERE id=?');$s->execute([$job['subscriber_id']]);if($s->fetchColumn()!=='active'){$s=$db->prepare("UPDATE newsletter_mail_jobs SET status='cancelled' WHERE id=?");$s->execute([$job['id']]);return 'cancelled';}}
            $mailer=ij_mail_message($job['recipient'],$message);
            if($transport){if($transport($mailer)!==true)throw new RuntimeException('Transport failed');}elseif(!$mailer->send())throw new RuntimeException('Transport failed');
            $s=$db->prepare("UPDATE newsletter_mail_jobs SET status='sent',sent_at=UTC_TIMESTAMP(),html=NULL WHERE id=?");$s->execute([$job['id']]);return 'sent';
        }catch(Throwable $e){$s=$db->prepare("UPDATE newsletter_mail_jobs SET status='failed',last_error='No se confirmó la entrega SMTP. Revisar antes de reintentar.' WHERE id=?");$s->execute([$job['id']]);error_log('INFO JURIDICA: fallo de envío SMTP. Job '.(int)$job['id']);return 'failed';}
    }finally{$s=$db->prepare('SELECT RELEASE_LOCK(?)');$s->execute([$lock]);}
}
