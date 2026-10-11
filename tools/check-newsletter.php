<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../app/mail.php';
$c=ij_mail_config();
echo 'PHPMailer: '.PHPMailer\PHPMailer\PHPMailer::VERSION.PHP_EOL;
echo 'OpenSSL: '.(extension_loaded('openssl')?'OK':'FALTA').PHP_EOL;
echo 'Contraseña SMTP: '.(!empty($c['smtp_password'])?'configurada':'falta').PHP_EOL;
echo 'Envíos: '.(ij_mail_enabled()?'habilitados':'deshabilitados').PHP_EOL;
echo 'Límite propio por 24 horas: '.max(1,min(90,(int)($c['daily_limit']??90))).PHP_EOL;
if (!in_array('--smtp',$argv,true)) {
    echo 'Esta comprobación no establece conexión SMTP ni envía mensajes.'.PHP_EOL;
    exit;
}
// Only connect and authenticate: never issue MAIL FROM, RCPT TO or DATA.
if (empty($c['smtp_password'])) { echo 'Diagnóstico: falta configurar la contraseña.'.PHP_EOL;exit(1); }
$smtp=new PHPMailer\PHPMailer\SMTP();$smtp->do_debug=0;$smtp->Timeout=15;$smtp->Timelimit=20;
$stage='conexión TLS';
try {
    $port=(int)$c['smtp_port'];
    if (!in_array($port,[465,587],true)) throw new RuntimeException('Puerto inválido');
    $stage=$port===465 ? 'conexión TLS' : 'conexión';
    if (!$smtp->connect(($port===465?'ssl://':'').$c['smtp_host'],$port,15)) throw new RuntimeException('Conexión');
    echo 'Conexión: OK'.PHP_EOL;
    $stage='saludo SMTP';
    if (!$smtp->hello('infojuridica.com.ar')) throw new RuntimeException('Saludo');
    if ($port===587) {
        $stage='conexión TLS';
        if (!$smtp->startTLS() || !$smtp->hello('infojuridica.com.ar')) throw new RuntimeException('TLS');
        echo 'TLS: OK'.PHP_EOL;
    }
    $stage='autenticación';
    if (!$smtp->authenticate($c['smtp_user'],$c['smtp_password'])) throw new RuntimeException('Autenticación');
    echo 'Autenticación SMTP: OK'.PHP_EOL;
    echo 'No se envió ningún correo. El mensaje fallido permanece sin reintento automático.'.PHP_EOL;
} catch (Throwable $e) {
    $error=$smtp->getError();
    $code=preg_replace('/[^0-9]/','',(string)($error['smtp_code']??''));
    echo 'Falló: '.$stage.($code ? ' (código SMTP '.$code.')' : '').PHP_EOL;
    echo 'No se muestran respuestas del servidor ni credenciales.'.PHP_EOL;
    exit(1);
} finally {
    if ($smtp->connected()) $smtp->quit();
    $smtp->close();
}
