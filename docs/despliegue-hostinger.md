# Publicación en Hostinger

Dominio previsto: https://www.infojuridica.com.ar. El push a GitHub activa el despliegue; un commit local no publica. La rama local actual es main; confirmar en Hostinger la rama y carpeta de destino antes del push.

## Preparación

1. Respaldar el sitio actual de Hostinger y exportar su base. Guardar fuera de public_html.
2. Exportar la base completa de MAMP desde phpMyAdmin, incluyendo estructura y datos: publicaciones, usuarios, autores, slugs, AHORA y posiciones de portada. No guardar ese archivo en Git ni en la raíz pública.
3. La base de Hostinger ya tiene tablas. No importar schema.sql otra vez ni importar encima un volcado que intente crear las mismas tablas. Preferir otra base vacía para restaurar el export completo y conservar la existente. Si se reutiliza la base instalada, verificar que esté realmente sin publicaciones y resolver explícitamente las tablas existentes antes de importar.
4. Transferir el contenido de uploads/ desde local a la misma carpeta en Hostinger. Está excluido de Git; hay 14 imágenes locales al preparar este despliegue. Mantener nombres y rutas. Confirmar que el autodeploy conserva esa carpeta entre versiones.
5. Crear config/local.php en el servidor desde config/example.php, con las credenciales de Hostinger: host, puerto, nombre completo de base, usuario y contraseña. socket debe ser null; no copiar el socket de MAMP. Mantener enabled false durante la preparación. Confirmar que el autodeploy conserva este archivo privado.
6. Verificar PHP 8.3+, PDO MySQL, mbstring, fileinfo y GD; permisos de uploads, HTTPS, display_errors desactivado y límites de subida suficientes. El directorio público debe contener index.php y .htaccess.

## Comprobación antes de activar

Desde SSH, en la raíz del proyecto y una vez disponibles los archivos, ejecutar:

```sh
php tools/check-production.php
```

Solo lee configuración y metadatos de la base. No cambia tablas, contenido ni usuarios; no imprime credenciales. Puede ejecutarse con enabled false. Verificar también la versión PHP usada por la web, que puede diferir de la CLI.

Confirmar en Hostinger cómo persisten config/local.php y uploads/ antes de pushear. Un autodeploy que reemplaza toda la carpeta puede perder archivos excluidos de Git. enabled false muestra la maqueta: no constituye un modo de mantenimiento del sitio.

## Publicación y revisión

Preparar el commit local excluyendo credenciales, exportaciones, copias HTML y archivos de macOS. Revisar el contenido antes del push. Retirar los .DS_Store ya versionados del índice en ese commit sin borrar los archivos personales.

El push publica código. Coordinar la restauración de base e imágenes y cambiar enabled a true cuando estén preparadas. Verificar home, noticia con slug, categorías, búsqueda, login, borrador, vista previa, edición, portada manual, AHORA y /padron/. Comprobar que config/, app/, database/ y tools/ no sean descargables por URL y que uploads no ejecute PHP.

Ante un fallo, restaurar el código anterior y su configuración. Restaurar la base solo si fue modificada y el respaldo corresponde al mismo momento que las imágenes.

## Estado de esta revisión

Revisión local de sintaxis: 60 archivos PHP sin errores y JavaScript sin errores de sintaxis. config/local.php y las imágenes subidas quedan fuera de Git. La conexión y las tablas de producción todavía requieren ejecutar el chequeo en Hostinger. No se hizo commit ni push.

Agenda incorpora la tabla events. El export completo desde MAMP debe incluirla; si aún no existe en una base instalada, importar solo database/events.sql o permitir su creación automática al acceder a Agenda.
