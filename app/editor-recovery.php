<?php
require_once __DIR__.'/bootstrap.php';
function ij_recovery_ready():void{static $ready=false;if(!$ready){ij_db()->exec(file_get_contents(__DIR__.'/../database/editor-recovery.sql'));ij_db()->exec('DELETE FROM editor_recovery WHERE saved_at < DATE_SUB(UTC_TIMESTAMP(),INTERVAL 7 DAY)');$ready=true;}}
function ij_recovery_key(string $key):bool{return (bool)preg_match('/^(?:post-[1-9][0-9]*|new-[a-f0-9]{32})$/D',$key);}
function ij_recovery_clear(int $userId,string $key):void{if(!ij_recovery_key($key))return;ij_recovery_ready();$s=ij_db()->prepare('DELETE FROM editor_recovery WHERE user_id=? AND editor_key=?');$s->execute([$userId,$key]);}
