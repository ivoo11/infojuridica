<?php
if (PHP_SAPI !== 'cli') { http_response_code(404);exit; }
require __DIR__.'/../app/bootstrap.php';
$db=ij_db();
if ($db->query('SHOW TABLES')->fetchColumn()!==false) exit("La base debe estar vacía. No se modificaron tablas existentes.\n");
$db->exec(file_get_contents(__DIR__.'/../database/schema.sql'));
echo "Esquema instalado. Creá el usuario con tools/create-admin.php.\n";
