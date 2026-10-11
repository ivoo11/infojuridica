(() => {
    document.querySelectorAll('[data-newsletter-signup]').forEach(form => {
        const button = form.querySelector('button[type=submit]');
        const status = form.querySelector('[role=status]');
        form.addEventListener('submit', async event => {
            event.preventDefault();
            if (button.disabled) return;
            const data = new FormData(form);
            data.set('consent', '1');
            button.disabled = true;
            status.hidden = false;
            status.textContent = 'Registrando…';
            try {
                const tokenResponse = await fetch('/newsletter.php?token=1', { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json' } });
                if (!tokenResponse.ok) throw new Error('No pudimos conectar. Intentá nuevamente.');
                const token = await tokenResponse.json();
                data.set('csrf', token.csrf);
                const response = await fetch('/newsletter.php', { method: 'POST', body: data, credentials: 'same-origin', headers: { Accept: 'application/json' } });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'No pudimos registrar la solicitud.');
                status.textContent = result.message;
                form.querySelector('[name=email]').value = '';
            } catch (error) {
                status.textContent = error instanceof SyntaxError ? 'No pudimos conectar. Intentá nuevamente.' : error.message;
            } finally { button.disabled = false; }
        });
    });
})();
