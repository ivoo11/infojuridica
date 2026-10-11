<section class="article-share" id="compartir" aria-labelledby="shareHeading">
    <div class="article-share-intro">
        <h2 id="shareHeading"><?= $isOpinion ? '¿Te gustó esta columna?' : '¿Te gustó esta nota?' ?></h2>
        <p>Compartila con alguien a quien le pueda interesar.</p>
    </div>
    <div class="article-share-actions">
        <a data-share="whatsapp" href="https://wa.me/" target="_blank" rel="noopener noreferrer">
            <svg class="share-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M20.5 11.8a8.7 8.7 0 0 1-13 7.6L3 21l1.5-4.4A8.7 8.7 0 1 1 20.5 11.8Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M8.4 7.4c-.6.4-.8 1-.7 1.7.3 2.7 3.2 5.6 5.9 6 .7.1 1.4-.2 1.8-.8l.5-1-2.1-1.1-.8.9c-1.4-.5-2.6-1.7-3.2-3.2l.8-.8-1.1-2.1-1.1.4Z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
            <span>WhatsApp</span>
        </a>
        <a data-share="x" href="https://twitter.com/intent/tweet" target="_blank" rel="noopener noreferrer" aria-label="Compartir en X">
            <svg class="share-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M18.9 2H22l-6.8 7.8L23.2 22h-6.3L12 14.5 5.4 22H2.2l8.3-9.5L2.8 2h6.5l4.4 6.8L18.9 2ZM17.4 20h2L8.2 4h-2L17.4 20Z"/></svg>
            <span>X</span>
        </a>
        <a data-share="facebook" href="https://www.facebook.com/sharer/sharer.php" target="_blank" rel="noopener noreferrer">
            <svg class="share-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M14 22v-9h3l.5-3H14V8c0-.9.3-1.5 1.6-1.5H18V3.3c-.7-.1-1.9-.3-3.2-.3-3.1 0-5 1.9-5 5V10H7v3h2.8v9H14Z"/></svg>
            <span>Facebook</span>
        </a>
        <button type="button" id="copyLink">
            <svg class="share-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><rect x="8" y="8" width="12" height="12" rx="1.5" stroke="currentColor" stroke-width="1.6"/><path d="M15 5V4H4v11h1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Copiar enlace</span>
        </button>
    </div>
    <p id="shareStatus" role="status" aria-live="polite"></p>
    <label class="article-copy-fallback" id="copyFallback" hidden>
        Enlace de la publicación
        <input type="url" id="copyFallbackInput" readonly>
    </label>
    <noscript><p class="article-share-help">Activá JavaScript para compartir el enlace de esta publicación.</p></noscript>
</section>
