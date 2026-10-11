<?php
// Copiar a local.php. No guardar credenciales en Git ni compartir ese archivo.
return [
    'enabled' => false,
    // Reservado para el próximo paso: conexión de la casilla. Mantener deshabilitado.
    'newsletter' => ['enabled' => false, 'from' => 'noreply@infojuridica.com.ar', 'smtp_host' => 'smtp.hostinger.com', 'smtp_port' => 465, 'smtp_user' => 'noreply@infojuridica.com.ar', 'smtp_password' => '', 'daily_limit' => 90],
    'db' => ['host' => 'localhost', 'port' => 3306, 'name' => 'usuario_infojuridica', 'user' => 'usuario_editor', 'password' => '', 'socket' => null],
];
