<?php
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../app/mail.php';
$results=ij_mail_process_batch();echo 'Newsletter: '.implode(', ',$results).PHP_EOL;
exit(in_array('failed',$results,true) ? 1 : 0);
