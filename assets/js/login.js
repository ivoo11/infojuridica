(() => {
    const form = document.querySelector('[data-login-form]');
    if (!form) return;
    const button = form.querySelector('button[type="submit"], button.primary');
    const error = form.querySelector('[data-login-error]');
    let pending = false;
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (pending) return;
        pending = true;
        button.disabled = true;
        button.textContent = 'Ingresando…';
        error.hidden = true;
        try {
            const url = new URL(form.action || location.href);
            url.searchParams.set('form_token', '1');
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 10000);
            let response;
            try {
                response = await fetch(url, {credentials: 'same-origin', cache: 'no-store', signal: controller.signal});
            } finally {
                clearTimeout(timeout);
            }
            if (!response.ok) throw new Error('token');
            const data = await response.json();
            if (typeof data.csrf !== 'string' || !/^[a-f0-9]{64}$/.test(data.csrf)) throw new Error('token');
            form.elements.csrf.value = data.csrf;
            HTMLFormElement.prototype.submit.call(form);
        } catch (_) {
            error.textContent = 'No pudimos conectar con el panel. Revisá tu conexión y volvé a tocar Ingresar.';
            error.hidden = false;
            pending = false;
            button.disabled = false;
            button.textContent = 'Ingresar';
        }
    });
    window.addEventListener('pageshow', () => {
        pending = false;
        button.disabled = false;
        button.textContent = 'Ingresar';
    });
})();
