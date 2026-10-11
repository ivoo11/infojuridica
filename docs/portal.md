> Estado posterior: ver [navegación, home y reloj GMT-3](navegacion.md). Este informe corresponde a la primera integración.

# INFO JURÍDICA — componentes compartidos y noticia

Integración local realizada el 10 de octubre de 2026. No se realizó ningún commit, push ni despliegue.

## Cambios

- `index.php` reutiliza `includes/header.php`, `includes/navbar.php` y `includes/footer.php`.
- El enlace de la nota principal abre `/noticia.php`.
- `noticia.php` incorpora título, bajada, autor, fecha, imagen, cuerpo, subtítulo y cita destacada. Su contenido está identificado como demostración y utiliza `noindex, nofollow`.
- `includes/noticia-head.php` define los metadatos y carga el CSS original y los estilos de noticia.
- `assets/css/noticia.css` utiliza las tipografías, colores y bordes de la portada. La composición se adapta a móvil y escritorio.
- `assets/js/noticia.js` arma los enlaces de WhatsApp, X y Facebook con la URL visible, sin fragmento. El botón copiar anuncia el resultado; si el navegador rechaza la copia, muestra el enlace seleccionado para copiar manualmente. Compartir requiere JavaScript y un enlace accesible al destinatario.
- `assets/css/navigation.css` y `assets/js/navigation.js` agregan apertura/cierre al botón MENÚ original, que antes carecía de manejador. Escape cierra menú y buscador. Los nuevos estilos se aplican al menú abierto.
- Los seis recursos CSS/JS que utiliza la noticia y los cuatro de portada conservan versionado automático con `filemtime()`.

## Conservación del original

El CSS `assets/css/styles.css`, el JavaScript `assets/js/app.js`, las imágenes y todos los archivos de código/recursos del padrón se conservaron byte por byte. No se cambiaron configuración del servidor, Git, API ni recursos del padrón. Los cambios de trabajo que ya existían en el repositorio fueron conservados.

La portada sigue teniendo sus enlaces provisionales `#`, la fecha original y la acción de búsqueda `/buscar/`, cuya página todavía no existe. Esta iteración no implementa búsqueda, newsletter ni CMS.

## Verificación

- Portada visualmente idéntica al original a 320px.
- Menú/buscador abren y Escape cierra a 320px.
- Noticia accesible desde la home, imagen cargada y sin desborde a 320px.
- Portada visualmente idéntica al original a 375px.
- Menú/buscador abren y Escape cierra a 375px.
- Noticia accesible desde la home, imagen cargada y sin desborde a 375px.
- Portada visualmente idéntica al original a 768px.
- Menú/buscador abren y Escape cierra a 768px.
- Noticia accesible desde la home, imagen cargada y sin desborde a 768px.
- Portada visualmente idéntica al original a 1440px.
- Menú/buscador abren y Escape cierra a 1440px.
- Noticia accesible desde la home, imagen cargada y sin desborde a 1440px.
- WhatsApp, X y Facebook comparten el título/enlace esperado.
- Copiar enlace: éxito y alternativa seleccionable ante permiso denegado.
- Padrón visualmente idéntico y validación frontend correcta a 375px.
- Padrón visualmente idéntico y validación frontend correcta a 1440px.
- API del padrón: rechaza JSON, DNI y sexo inválidos sin consultar la fuente externa.
- Sin errores JavaScript ni recursos locales faltantes.
- Sintaxis de todos los PHP, incluidos los del padrón: correcta.
- Render PHP sin avisos y versiones `filemtime()` coincidentes con los archivos: correcto.
- La comparación del HTML original y el reconstruido confirma que solo cambiaron las inclusiones, los atributos del menú, el enlace principal y la carga de los nuevos recursos.
- Comprobación de identidad por SHA-256 de todos los archivos originales del padrón: correcta.
- Prueba de caracteres especiales en título/enlaces de compartir: correcta.

Las capturas comparadas cargaron las imágenes y fuentes externas. El portapapeles se simuló dentro del navegador para verificar éxito y denegación sin alterar el portapapeles personal. La API del padrón se probó con entradas inválidas; no se efectuó una consulta con DNI válido a su fuente externa. Esa dependencia externa no fue certificada.

## Cómo revisar

Abrir `/` y `/noticia.php` desde el servidor local habitual con esta carpeta como raíz pública. La nota principal de la portada permite llegar a la noticia. `/padron/` conserva su acceso independiente.

La carpeta debe usarse como raíz pública, igual que antes de esta integración: los enlaces originales empiezan con `/`. Para una ejecución temporal desde esta carpeta, puede usarse `php -S 127.0.0.1:8765 -t .` y abrir `http://127.0.0.1:8765/noticia.php`.

Las pruebas automatizadas, las capturas y una copia del código previo quedaron en el espacio de trabajo de esta conversación; no se copiaron al sitio público. Los servidores usados para las pruebas fueron cerrados al finalizar.
