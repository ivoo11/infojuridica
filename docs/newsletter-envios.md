# Envíos del newsletter

## Configuración privada en Hostinger

El código incluye PHPMailer y su autoloader para que el autodeploy no dependa de ejecutar Composer en Hostinger. No hay contraseñas en Git. La configuración MySQL config/local.php no se modifica.

Dentro de /home/u736192581/domains/infojuridica.com.ar/public_html:

```bash
cp -n config/mail.example.php config/mail.local.php
chmod 600 config/mail.local.php
nano config/mail.local.php
```

Completar smtp_password con la contraseña de noreply@infojuridica.com.ar en el servidor; nunca compartirla por chat. Mantener enabled=false durante la preparación. Hostinger SMTP usa smtp.hostinger.com, puerto 465 con TLS implícito. El código también admite 587 con STARTTLS. Ver https://www.hostinger.com/support/4305847-set-up-hostinger-email-on-your-applications-and-devices/ .

Verificar primero la configuración sin enviar un correo:

```bash
/opt/alt/php83/usr/bin/php tools/check-newsletter.php
```

Después de completar la contraseña, establecer enabled=true. Esto habilita el envío, pero NO crea campañas ni emite correos por sí solo. El archivo mail.local.php está excluido de Git y protegido por config/.htaccess.

## Proceso programado

Crear en Hostinger → Cron Jobs una tarea cada minuto con este comando, usando PHP 8.3 y la carpeta correcta:

```bash
/opt/alt/php83/usr/bin/php /home/u736192581/domains/infojuridica.com.ar/public_html/tools/newsletter-worker.php
```

El worker procesa un solo correo por ejecución. Comprueba hasta 90 intentos en las últimas 24 horas y al menos 60 segundos desde el intento anterior. Pruebas y campañas comparten el límite. Si la casilla se usa fuera de esta aplicación, esos envíos no pueden contabilizarse aquí: usar una casilla dedicada y conservar margen respecto del límite del proveedor. Los límites originales se verificaron en https://www.hostinger.com/support/4625828-parameters-and-limits-of-hostinger-email/ .

Antes de la primera prueba puede ejecutarse el worker a mano; sin mensajes preparados responde empty.

## Circuito editorial

Newsletter → Enviar → Enviar prueba agrega un correo individual a la cola. No suscribe al destinatario y no manda mensajes a otros contactos. La edición de prueba contiene un pie aclaratorio; no tiene un token real de baja. El destinatario debe ser elegido expresamente en el CMS.

Enviar edición exige marcar que se revisó el contenido. Se congela la edición, noticias y agenda en un snapshot. Los destinatarios son los activos en ese momento. La misma edición no se puede preparar dos veces; para un próximo newsletter crear una edición nueva. Los cambios posteriores en el borrador no modifican una campaña preparada. El worker vuelve a comprobar el estado activo antes de cada correo.

Los errores SMTP quedan failed y nunca se reintentan automáticamente, porque una desconexión puede ocurrir después de que el servidor haya aceptado un correo. Si una ejecución se interrumpe, el mensaje queda processing: revisar con el proveedor antes de restaurarlo a queued para evitar duplicados. Enviar significa aceptado por el servidor SMTP; no garantiza que haya llegado a bandeja de entrada. No hay medición de aperturas ni gestión automática de rebotes en esta etapa.

## Altas sin correo de confirmación

Por decisión editorial, el formulario activa la suscripción al aceptar mediante el botón. Registra texto, versión, origen y fecha. No se verifica propiedad del correo, no se envían confirmaciones y confirmed_at permanece NULL. En el CMS se muestra Suscripto en lugar de Confirmado. Las solicitudes anteriores, creadas bajo el circuito pendiente, permanecen pendientes: pueden activarse expresamente desde Ver suscriptores si tienen consentimiento registrado.

Las bajas existentes se mantienen: enlace personal + confirmación sin sesión, y POST One-Click para clientes compatibles. Una nueva solicitud tras una baja vuelve a active por la nueva aceptación expresa, invalida los tokens anteriores y no hace pasar ese acto por una verificación de email. Un atacante puede registrar una dirección ajena; se mantienen validaciones, tokens de formulario, campo trampa, límites y baja sencilla.

## Activación y pruebas

Las pruebas locales usan exclusivamente una base temporal y un transporte simulado: se verifica MIME, TLS configurado, estados de cola, límites, snapshot, exclusión de bajas y duplicados. No se han enviado correos reales ni probado todavía las credenciales del servidor. Los datos del responsable y el contacto de privacidad siguen pendientes de completar; el texto de aceptación no sustituye esa información.
