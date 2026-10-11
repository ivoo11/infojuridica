# CMS de INFO JURÍDICA — primera etapa

Implementado en local; no se publicó ni se conectó al hosting. El portal conserva su modo de demostración hasta habilitar la conexión. No se importan artículos ficticios a la base real.

## Qué permite

- Ingresar a `/admin/` con un usuario privado; cerrar sesión y caducidad de 30 minutos de inactividad.
- Crear/editar noticias y columnas de opinión. Guardar borradores, consultar su vista previa privada, publicar ahora o indicar una fecha futura (Argentina, GMT-3).
- Retirar una publicación volviéndola a borrador, sin borrar contenido.
- Cargar autores con nombre, cargo y fotografía; seleccionarlos en las publicaciones.
- Subir/reemplazar imagen, descripción accesible y epígrafe/crédito. JPG, PNG y WebP, hasta 5 MB y 20 megapíxeles. Se convierte a JPG y reduce a un máximo de 2000 px; las transparencias quedan sobre blanco.
- Elegir destacados y En contexto, con opciones de ubicación: Últimas noticias, Noticia principal o Destacada junto a la principal. El primer destacado es la noticia principal; el resto ocupa hasta tres lugares laterales. Si no hay destacados, se usa la noticia más reciente. Últimas noticias muestra hasta seis noticias; Opinión, hasta tres columnas; En contexto, hasta tres publicaciones seleccionadas.
- Las publicaciones visibles alimentan portada, noticia/opinión, categorías y búsqueda. Los borradores y fechas futuras no son públicos. La fecha futura se evalúa al cargar la página y no requiere cron.
- Seleccionar un párrafo y aplicar Cita destacada, Subtítulo o Párrafo con los botones del editor; consultar la vista del formato. Internamente se conservan los marcadores de texto compatibles con las notas existentes. Se guarda texto plano; HTML no se ejecuta. Un editor enriquecido, enlaces editoriales e inserción de imágenes dentro del cuerpo quedan pendientes.

El compartir sigue al final del artículo. Se conserva el versionado CSS/JS con `filemtime()`. La configuración `app/home-settings.php` sigue controlando AHORA y publicidad; Agenda se oculta al activar la base para evitar publicar actividades ficticias. Su administración, publicidad, usuarios/roles y SEO avanzado son etapas posteriores.

## Requisitos

PHP 8.3 o posterior (usar una versión con soporte vigente: https://www.php.net/supported-versions.php), extensiones PDO MySQL, mbstring, fileinfo y GD (con JPG/PNG/WebP), MySQL 8 o MariaDB 10.6+, Apache con `.htaccess` y HTTPS en el hosting. No requiere Composer. `uploads/` debe ser escribible por PHP; no usar permisos 777.

## Instalación en cPanel (cuando se autorice el despliegue)

1. Guardar una copia del sitio y de cualquier base existente. Crear una **base vacía nueva**, por ejemplo `cuenta_infojuridica`, en Bases de datos MySQL. Crear un usuario y asignarlo exclusivamente a esa base con sus privilegios. cPanel agrega un prefijo al nombre: copiar los nombres completos.
2. Importar `database/schema.sql` en esa base desde phpMyAdmin. No importar sobre una base que ya tenga estas tablas. El esquema contiene usuarios, categorías, autores, publicaciones y límite de intentos de acceso; no contiene contraseñas ni publicaciones de muestra.
3. Copiar `config/example.php` a `config/local.php` y completar host (habitualmente `localhost`), puerto, nombre completo de base, usuario y contraseña. Mantener `enabled => false` hasta terminar la preparación. No enviar credenciales por chat ni guardar este archivo en Git.
4. Crear el primer usuario desde Terminal de cPanel, estando dentro de la raíz del proyecto:

   ```sh
   php tools/create-admin.php editor@tu-dominio.com "Tu nombre"
   ```

   La contraseña se ingresa de forma privada y se guarda únicamente su hash. Usar entre 12 y 72 bytes. Si la base todavía está vacía y no se importó desde phpMyAdmin, `php tools/install.php` instala el esquema y se niega a trabajar sobre una base con tablas.
5. Si cPanel no ofrece Terminal, ejecutar **en la computadora local**:

   ```sh
   php tools/create-admin.php --sql editor@tu-dominio.com "Tu nombre"
   ```

   Copiar únicamente la sentencia `INSERT INTO users ...` generada y ejecutarla en la pestaña SQL de phpMyAdmin de la base nueva. No publicar esa sentencia ni la contraseña. El comando no se conecta a ninguna base en modo `--sql`.
6. Subir los archivos del proyecto con la raíz pública correcta, los `.htaccess` de `config/`, `app/`, `database/`, `tools/`, `admin/` y `uploads/`. No subir copias de trabajo, capturas, pruebas ni contenido de `.git`. El `.htaccess` de `uploads/` prohíbe ejecución de scripts y listado de archivos; los directorios privados no son descargables desde Apache.
7. Cambiar `enabled` a `true`. Ingresar a `/admin/`. Crear un borrador con imagen y probar vista previa. Publicarlo y comprobar portada, categoría, búsqueda, compartir y `/padron/`. La base vacía presenta una portada de inicio sin noticias ficticias; cargar contenido real antes de abrir el portal al público.
8. Antes de dar por terminado el despliegue, verificar HTTPS, PHP sin `display_errors`, límites de subida suficientes (por ejemplo `upload_max_filesize=6M` y `post_max_size=8M`), permisos de `uploads/` y que acceder por URL a los directorios privados devuelva 403. Apache/cPanel no se probó desde esta computadora.

No hay instalador web ni registro público de usuarios. Para crear otro editor, repetir el comando con otro correo. Esta primera versión tiene un único nivel de acceso editorial; no ofrece roles diferenciados ni una interfaz de recuperación de contraseña.

## Copias de seguridad

Exportar la base completa desde phpMyAdmin y guardar `uploads/`, `config/local.php` y el código. Base e imágenes deben respaldarse juntas. Mantener copias fuera de la raíz pública. Probar una restauración en otra base antes de depender del respaldo. Retirar una publicación no elimina su imagen; limpieza de archivos y respaldo automatizado están pendientes.

## Pruebas locales

Se probó el esquema en una instancia MySQL aislada y sin red, usando datos ficticios y una configuración temporal externa al proyecto (`INFOJURIDICA_CONFIG`). El entorno de prueba no habilita la base en el sitio habitual. Resultados y capturas están en el espacio de trabajo de esta conversación.

La base se guarda en UTC; el editor y la fecha pública usan Argentina. No se consultó ni modificó la API externa del padrón. No se hicieron commits, push ni despliegue.

## Uso del editor actualizado

- En Publicaciones, usar Editar para modificar una nota ya publicada. Guardar cambios actualiza su contenido conservando fecha y estado.
- Guardar borrador admite una nota incompleta con título; no la hace pública.
- Publicar ahora exige bajada/cuerpo y una imagen para noticias (autor para Opinión). Ignora una programación anterior del borrador y publica en el momento.
- Pasar a borrador retira una nota publicada.
- Programar para más adelante abre la fecha GMT-3 y la acción Programar publicación; requiere una fecha futura.
- Seleccionar un párrafo y pulsar Cita destacada aplica el estilo con la línea roja en la vista del editor y en el artículo.
- No hay campo numérico Orden en portada. Las ubicaciones son opciones descriptivas. Al elegir una nueva noticia principal publicada, la principal anterior pasa al grupo de destacados.

No hay cambios de esquema ni migración de la base para esta mejora. La ruta del logo compartido usa `/assets/img/logo-infojuridica.png`, válida también desde las categorías.

## Barra AHORA

En el panel, AHORA permite editar el texto y un enlace opcional. Vaciar el texto y guardar oculta la barra sin dejar espacio. Los cambios se guardan en MySQL. La tabla site_settings se crea automáticamente si todavía no existe (requiere permiso CREATE en la base); también puede importarse database/settings.sql. El aviso anterior se conserva hasta la primera edición. No hace falta volver a importar schema.sql sobre una base instalada.

## Archivo con fecha anterior

En el editor, desplegar Publicar con fecha anterior, indicar fecha/hora de Argentina y usar Publicar con esta fecha (Guardar con esta fecha para una nota ya visible). La publicación queda visible inmediatamente y Últimas noticias la ordena por la fecha seleccionada. Se conserva created_at como registro interno de creación. No requiere migración. Publicar ahora mantiene su comportamiento; Guardar cambios conserva la fecha existente y la programación futura sigue siendo una acción separada. Guardar borrador no aplica la fecha anterior.

## Enlaces legibles

Las publicaciones reales usan /noticias/slug y /opinion/slug. El CMS genera un slug desde el título, sin tildes ni espacios, y resuelve títulos repetidos con sufijos. En Enlace de la publicación puede editarse. Cambiar el título no cambia automáticamente un enlace existente. Los slugs anteriores y los enlaces noticia.php?id=… redirigen al actual; los borradores y fechas futuras siguen siendo privados.

Las tablas post_slugs y post_urls se crean automáticamente al activar la función (requiere CREATE). Los registros existentes reciben su slug sin modificar sus fechas, estado ni contenido. No reimportar schema.sql; database/slugs.sql permite preparar las tablas manualmente. En MAMP/Apache se usa .htaccess y mod_rewrite; para pruebas con php -S usar tools/router.php como router. Los enlaces de la maqueta continúan usando los IDs.

## Principal y laterales independientes

La pantalla Portada tiene cuatro posiciones manuales: principal y lateral 1/2/3. Publicar o cambiar fecha de una noticia no modifica esa selección. No hay relleno automático con noticias recientes. Una posición sin seleccionar, o cuya noticia pase a borrador, queda vacía. Todas las noticias visibles siguen en Últimas noticias por fecha (seis en home; archivo paginado en /noticias/).

La tabla home_slots se crea automáticamente con las selecciones destacadas existentes, sin rellenar posiciones desde Últimas noticias. No reimportar schema.sql; database/home-slots.sql es la opción manual. Los antiguos campos featured/home_order quedan conservados como datos históricos de compatibilidad; las selecciones actuales se guardan en home_slots. El editor de noticia enlaza a Portada; no administra posiciones desde la publicación.

## Agenda

La pantalla Agenda permite crear actividades con título, descripción, fecha/horario de inicio y finalización opcional (GMT-3), modalidad, lugar o plataforma, imagen opcional y enlace de inscripción. Guardar borrador requiere título; Publicar ahora exige descripción, inicio y lugar. Editar permite Guardar cambios o Pasar a borrador. Cancelar cambios confirma si hay modificaciones pendientes. La vista previa se abre en otra pestaña y requiere sesión editorial.

Las actividades publicadas aparecen en el carrusel de portada y /agenda/ ordenadas por inicio. Finalizado el evento se retira de próximas actividades sin borrarlo. Si no hay finalización, se conserva hasta el fin del día de inicio en Argentina. Con la base activa y sin actividades próximas, la sección de portada se oculta. Las actividades pasadas publicadas conservan su página directa.

La tabla events se crea automáticamente al usar Agenda; en una base existente puede importarse únicamente database/events.sql. No reimportar schema.sql. No se agregan actividades ficticias a la base real.

## Negritas dentro del cuerpo

Seleccionar palabras o una frase dentro del párrafo y pulsar Negrita (Ctrl+B o ⌘B). Repetir sobre la selección para quitarla. Se conserva como **texto** y se representa en negrita en la vista previa y en el artículo, incluidas las citas. El pegado de texto con negritas también conserva ese formato. No requiere cambios de base; HTML sigue escapado.

## Actividades con varias jornadas

Fechas y horarios permite agregar un día por fila, con fecha y horas de inicio/cierre separadas y opcionales. Agregar otra fecha conserva los horarios de la última fila para facilitar jornadas iguales. Cada día puede tener otro horario. No se muestra medianoche como hora si no fue indicada. Las actividades se mantienen en próximas hasta la última jornada (cierre conocido o fin del día).

La tabla adicional event_days se crea automáticamente (permiso CREATE), o importar solo database/event-days.sql en la base existente. No reimportar schema.sql. Los eventos anteriores conservan sus fechas y se muestran con compatibilidad; para convertir un rango continuo en jornadas, editar y confirmar las fechas y horarios.


## Newsletter: preparación editorial

Desde Newsletter → Nueva edición se propone una principal y cinco noticias, tomadas de las publicaciones visibles más recientes. Se pueden reemplazar, quitar o reordenar usando los seis lugares. Se excluyen borradores, publicaciones programadas y columnas de opinión. La selección del newsletter no modifica la portada. Por ahora se proponen las seis más recientes; no se lleva un historial de noticias enviadas porque el envío aún no está implementado.

Guardar borrador vuelve al listado. Guardar y ver preview guarda la edición y abre su vista previa en otra pestaña; la pestaña original queda vinculada al mismo borrador. Cancelar cambios confirma si se descarta contenido modificado. Las vistas previas requieren sesión de editor y no son públicas.

La plantilla usa tablas, estilos en línea, imágenes y enlaces absolutos del dominio de producción. Incluye una introducción opcional y hasta tres próximas actividades; la agenda se oculta si no hay actividades. Al editar, se usan los contenidos vigentes de las noticias; todavía no se congela una versión para enviar.

Esta etapa NO envía correos: no hay botón de envío, conexión SMTP, programación ni baja pública. El registro inicial de suscriptores se describe a continuación. El pie muestra una aclaración de vista previa, no un enlace ficticio de baja. El próximo paso es implementar estos componentes con noreply@infojuridica.com.ar y límite inicial de 90 destinatarios por 24 horas, más envíos de prueba contabilizados. La configuración newsletter de config/example.php es únicamente una reserva deshabilitada para esa integración. No copiar ni reemplazar la configuración completa de una base ya configurada. No guardar contraseñas de correo en Git.


## Solicitudes de suscripción y consentimiento

El bloque compartido antes del footer abre /newsletter.php. No se muestra un popup. El formulario exige email válido y una casilla no premarcada. La respuesta deja claro que la solicitud queda pendiente y que el correo de confirmación se enviará cuando se conecte el servicio. No se confirma la propiedad del correo por completar el formulario y no se lo habilita para envíos.

Al ingresar una solicitud se crean automáticamente las tablas de database/newsletter-subscribers.sql, sin reemplazar las tablas existentes. newsletter_subscribers guarda email único, estado pending, fecha de solicitud y campos reservados para confirmación y baja. newsletter_consents registra cada aceptación de una solicitud pendiente con versión 2026-10-11, texto exacto, origen portal-form y fecha UTC. Repetir una solicitud no duplica al suscriptor ni borra las aceptaciones anteriores. Una solicitud nueva después de una baja nunca vuelve a activar directamente el correo.

Protecciones: token de formulario, cookie de sesión separada del administrador, campo trampa y límite de cinco solicitudes válidas por IP/hora. La dirección IP no se almacena en texto: se usa una huella temporal para limitar solicitudes, eliminada tras dos horas. La aceptación no reemplaza la verificación del correo.

Newsletter → Ver suscriptores muestra hasta 50 por página y requiere sesión de editor. No permite activar manualmente solicitudes sin confirmación. Pendientes: envío de confirmación, enlace con token y vencimiento, baja personal y envío SMTP. No iniciar campañas con correos pending. Antes de enviar también debe completarse la información de privacidad y contacto aplicable al servicio.


### Formulario integrado en el banner (versión vigente)

El banner compartido permite ingresar email y pulsar Suscribirme sin navegar a otra página. El texto junto al botón explicita la autorización de recibir el newsletter y la posibilidad de darse de baja. No hay casilla en este banner; el formulario independiente conserva su aceptación por casilla como alternativa sin JavaScript.

El envío del banner obtiene un token de sesión no cacheable de /newsletter.php?token=1 y registra la solicitud con respuesta JSON en la misma página. No se incluyen tokens de sesión en el HTML de páginas que puedan cachearse. Se mantienen validación, protección de formulario, campo trampa y límite de solicitudes.

El consentimiento del banner tiene versión 2026-10-11-button y origen portal-banner; se guarda el texto exacto visible. Los consentimientos previos conservan su versión y texto. Las solicitudes siguen pendientes de confirmar: no se emiten correos todavía. La baja automática mediante enlace personal deberá integrarse antes del envío. El aviso junto al botón no sustituye la información del responsable de datos y sus canales de contacto; esos datos todavía no fueron suministrados. Esta implementación técnica no constituye una certificación de cumplimiento legal.
