# SEO técnico

- Sitemap público dinámico: https://infojuridica.com.ar/sitemap.xml. Se actualiza con el CMS; no requiere cron. Incluye notas y opiniones publicadas con fecha alcanzada, categorías, listados, padrón y actividades publicadas. Excluye borradores, ocultas, programadas, administración y resultados de búsqueda. Se divide si supera 40.000 URL.
- robots.txt anuncia el sitemap y excluye rutas internas; no sustituye sus protecciones de acceso. No bloquea el rastreo del buscador, para que Google pueda leer su noindex.
- La portada publica Organization y WebSite. Las noticias publican NewsArticle; las opiniones Article. Ambas incluyen autor, fechas UTC, imagen cuando existe y BreadcrumbList. Las notas ocultas y las vistas previas no publican estos datos.
- Los listados tienen canonical propio por página. Buscar devuelve noindex, follow. www redirige a la versión sin www para GET/HEAD, conservando consultas; POST no se redirige.
- En Search Console: verificar la propiedad de dominio con el TXT que Google asigne, enviar sitemap.xml y revisar Indexación de páginas e Inspección de URL. No se puede garantizar indexación ni posición; validar también Core Web Vitals y resultados enriquecidos.

Referencias: https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview y https://developers.google.com/search/docs/appearance/structured-data/article .
