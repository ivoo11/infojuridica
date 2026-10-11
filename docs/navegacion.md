# INFO JURÍDICA — navegación, home y reloj GMT-3

Segunda iteración local, 10 de octubre de 2026. Reemplaza el estado de la home descrito en el informe de la primera integración.

## Barra superior

La esquina izquierda muestra solo la fecha actual de Argentina, sin hora ni indicador visible GMT. Se calcula en `America/Argentina/Buenos_Aires` (GMT-3). PHP entrega la fecha inicial y `assets/js/clock.js` cambia la fecha al llegar la medianoche de Argentina, sin recargar la página. Sin JavaScript queda visible la fecha del momento de carga. Se retiraron Newsletter, Contacto y Quiénes somos de la esquina derecha.

## Acciones y botones

Se retiraron las flechas decorativas de Buscar, del aviso AHORA y de Volver a la portada. Los enlaces y acciones conservan las tipografías, colores, bordes y proporciones del diseño. El carrusel de Agenda usa controles de icono con chevrones discretos, sin texto combinado con flechas; conserva sus etiquetas accesibles y el área táctil de 44 px.

Este ajuste pasó la verificación visual en 375 y 1440 px, y la fecha se verificó con un navegador configurado en Asia/Tokyo para comprobar que sigue correspondiendo a Argentina. La prueba de medianoche comprobó el cambio de fecha sin mostrar segundos ni hora. No hubo errores PHP/JavaScript y los recursos originales del padrón siguen intactos.

## Navegación y portada

- Navbar de seis opciones: Justicia, Profesión, Colegiatura, Universidades, Agenda y Opinión.
- Las categorías tienen destinos reales y comparten una plantilla con hasta seis cards por página. El contenido proviene de la maqueta, identificado como demostración.
- Agenda y Opinión apuntan a `/#agenda` y `/#opinion`, también desde páginas interiores.
- El bloque negro conserva sus colores, columnas y separadores; ahora muestra tres columnas de opinión con un espacio reservado para el retrato y datos del autor.
- Agenda es un carrusel horizontal: flechas en escritorio, desplazamiento nativo horizontal en móvil y acceso con teclado.
- Se retiraron Jurisprudencia, el bloque separado de Elecciones, Radar Federal, Mercado Jurídico, Herramientas y Newsletter.
- Últimas noticias conserva seis cards y En contexto conserva tres. Se quitaron Ver todas/Ver todos.
- Los enlaces de las cards llevan a las correspondientes noticias/columnas de demostración. El footer refleja las categorías y secciones disponibles, con acceso al padrón.
- El aviso AHORA y la publicidad tienen una configuración local inicial en `app/home-settings.php`. AHORA puede ocultarse con `null`; la publicidad está desactivada y no deja un recuadro vacío. Su administración desde el CMS sigue pendiente.
- La búsqueda general consulta las publicaciones de la maqueta, incluidas las columnas. Admite paginación y búsquedas sin tildes. Todavía no consulta MySQL.

## Estado del contenido

Los textos de las noticias provienen de la maqueta; los cuerpos siguen siendo de demostración. Opinión usa autores de demostración y una silueta reservada para la fotografía real. Las actividades y fechas de Agenda son ficticias y están rotuladas como demostración: no son información de eventos reales ni confirmados. No se incorporaron anunciantes ni datos de contacto inventados.

La retirada automática de eventos vencidos, ocultar Agenda si no hay próximos eventos, las publicaciones programadas y los destacados se implementarán al conectar el CMS. Actualmente hay cuatro actividades ficticias para comprobar el carrusel.

## Pruebas

- Home 320px: fecha de Argentina, seis enlaces correctos, sin enlaces superiores ni desbordes.
- Menú, buscador y carrusel de Agenda operativos a 320px.
- Home 375px: fecha de Argentina, seis enlaces correctos, sin enlaces superiores ni desbordes.
- Menú, buscador y carrusel de Agenda operativos a 375px.
- Home 768px: fecha de Argentina, seis enlaces correctos, sin enlaces superiores ni desbordes.
- Menú, buscador y carrusel de Agenda operativos a 768px.
- Home 1440px: fecha de Argentina, seis enlaces correctos, sin enlaces superiores ni desbordes.
- Menú, buscador y carrusel de Agenda operativos a 1440px.
- Tres columnas de opinión conectadas a páginas individuales con autor de demostración.
- Las cuatro categorías responden y enlazan noticias existentes usando una plantilla común.
- Agenda y Opinión regresan a su sección de la home desde una noticia.
- Buscador de maqueta: acentos, paginación, consulta vacía y texto escapado.
- Publicaciones y categorías inexistentes devuelven 404.
- Fecha de Argentina disponible desde PHP aunque JavaScript esté desactivado.
- Sin avisos PHP, errores JavaScript ni recursos locales faltantes.
- Fecha: cambio de día a medianoche GMT-3 comprobado con un instante controlado.
- Sintaxis y render de PHP correctos en home, noticia, opinión, cuatro categorías y búsqueda.
- Todas las URLs CSS/JS propias utilizan `filemtime()` con valores verificados contra el archivo real.
- CSS base, JavaScript original, imágenes originales y todos los archivos del padrón permanecen idénticos a los originales.
- Compartir/copia conserva las comprobaciones de la primera iteración y pasó nuevamente su prueba de caracteres especiales y copia/denegación.

No se hizo commit, push ni despliegue. Los servidores temporales de prueba fueron cerrados al terminar. La fuente externa del padrón no se consultó en esta iteración.

## Revisión local

Abrir la home desde el servidor local habitual. Las cuatro categorías, Opinión, Agenda, el buscador y las cards ya pueden recorrerse desde la navegación.

El proyecto utiliza rutas desde `/`, como antes: esta carpeta debe configurarse como raíz pública. Para una prueba temporal desde la carpeta del proyecto se puede ejecutar `php -S 127.0.0.1:8765 -t .`.

El siguiente paso es revisar esta composición con contenido/fotografías reales y conectar MySQL y el gestor de publicaciones, autores, agenda y portada.

## Cierre para compartir

Compartir aparece al final del cuerpo de noticias y columnas, antes de volver a la portada. El bloque pregunta «¿Te gustó esta nota?» o «¿Te gustó esta columna?» e invita a compartirla con alguien a quien le pueda interesar. Conserva las fuentes y colores del sitio, con iconos monocromos y cuatro acciones: WhatsApp, X, Facebook y copiar enlace. En móvil se distribuyen en dos columnas.

Comprobado en noticias y opinión a 320, 375, 768 y 1440 px: posición, ausencia de desbordes, tamaño táctil y destinos correctos. Copia comprobada con éxito y con permiso denegado; el segundo caso muestra el enlace seleccionado. Sin errores JavaScript, avisos PHP ni recursos locales faltantes. Se mantiene el versionado con filemtime y no se modificó el padrón ni producción.

Resultados: `tests/share-refinement-results.json`. Capturas: `preview/compartir-noticia-1440.png`, `preview/compartir-noticia-375.png` y sus equivalentes de opinión.

## Primera etapa del CMS y MySQL

Panel privado `/admin/`: noticias y columnas, autores con fotografías, subida optimizada de imágenes, borradores, vista previa, publicación inmediata o futura y selección/orden de destacados y En contexto. Las publicaciones de la base alimentan portada, categorías y búsqueda. El modo de demostración sigue activo hasta configurar la base definitiva; no hay conexión al hosting.

Esquema: `database/schema.sql`. Configuración: copiar `config/example.php` a `config/local.php` y completar los datos de una base nueva. Instalación y primer usuario: `docs/cms.md`, `tools/install.php` y `tools/create-admin.php`. Hay una alternativa de SQL generado localmente para cPanel sin Terminal. No hay registro público ni instalador web.

Probado con MySQL 9.6 aislado, PHP 8.5.7 y navegador: ingreso/salida, cookie de sesión, límite de intentos, CSRF, HTML escapado, archivos inválidos, fechas imposibles, borradores privados, notas futuras ocultas, publicación/retiro, autores, búsqueda y responsive a 375/1440 px. También error de conexión 503 sin credenciales ni sustitución por contenido ficticio. Instalador rechaza bases con tablas. CSS/JS originales, imágenes originales y padrón mantienen sus hashes. Compartir y filemtime siguen verificados.

El editor usa texto con párrafos/subtítulos/citas; editor enriquecido y enlaces editoriales, gestión de agenda/AHORA/publicidad, roles, recuperación de contraseña y copias automáticas quedan pendientes. Apache, MariaDB y cPanel deberán verificarse en el hosting. No se publicó ni se crearon commits.
