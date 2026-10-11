<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../app/mail.php';
$result=ij_mail_process();echo 'Newsletter: '.$result.PHP_EOL;
exit($result==='failed' ? 1 : 0);
