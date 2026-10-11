(() => {
    const url = new URL(window.location.href);
    url.hash = '';
    const title = document.querySelector('.article-heading h1').textContent.trim();
    const destinations = {
        whatsapp: `https://wa.me/?text=${encodeURIComponent(title + '\n' + url.href)}`,
        x: `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url.href)}`,
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url.href)}`
    };
    document.querySelectorAll('[data-share]').forEach(link => { link.href = destinations[link.dataset.share]; });
    document.querySelector('#shareArticle').addEventListener('click', async () => {
        const status = document.querySelector('#shareStatus');
        const fallback = document.querySelector('#copyFallback');
        const input = document.querySelector('#copyFallbackInput');
        status.textContent = '';
        fallback.hidden = true;
        if (typeof navigator.share === 'function') {
            try {
                await navigator.share({ title, url: url.href });
                return;
            } catch (error) {
                // Closing the device's chooser is intentional, not a copy request.
                if (error.name === 'AbortError') return;
            }
        }
        try {
            await navigator.clipboard.writeText(url.href);
            fallback.hidden = true;
            status.textContent = 'Enlace copiado.';
        } catch (_) {
            fallback.hidden = false;
            input.value = url.href;
            input.focus();
            input.select();
            status.textContent = 'No se pudo copiar automáticamente. Seleccioná y copiá este enlace.';
        }
    });
})();
