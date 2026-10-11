<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../app/mail.php';
$c=ij_mail_config();
echo 'PHPMailer: '.PHPMailer\PHPMailer\PHPMailer::VERSION.PHP_EOL;
echo 'OpenSSL: '.(extension_loaded('openssl')?'OK':'FALTA').PHP_EOL;
echo 'Contraseña SMTP: '.(!empty($c['smtp_password'])?'configurada':'falta').PHP_EOL;
echo 'Envíos: '.(ij_mail_enabled()?'habilitados':'deshabilitados').PHP_EOL;
echo 'Límite propio por 24 horas: '.max(1,min(90,(int)($c['daily_limit']??90))).PHP_EOL;
echo 'Esta comprobación no establece conexión SMTP ni envía mensajes.'.PHP_EOL;
