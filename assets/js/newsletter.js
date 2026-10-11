(() => {
    const form = document.getElementById('newsletterEditor');
    if (!form) return;
    const snapshot = () => JSON.stringify([...new FormData(form)]);
    let saved = snapshot(), pending = saved;
    form.addEventListener('submit', event => {
        if (event.submitter?.value !== 'preview') return;
        pending = snapshot();
        const target = 'newsletterPreview' + Date.now();
        const preview = window.open('about:blank', target);
        if (preview) event.submitter.setAttribute('formtarget', target);
    });
    window.addEventListener('message', event => {
        if (event.origin !== location.origin || event.data?.type !== 'newsletter-saved' || !Number.isInteger(event.data.id) || event.data.id < 1) return;
        const url = `/admin/newsletter-editar.php?id=${event.data.id}`;
        form.setAttribute('action', url);
        history.replaceState(null, '', url);
        saved = pending;
    });
    document.getElementById('cancelNewsletter')?.addEventListener('click', event => {
        if (snapshot() !== saved && !confirm('¿Descartar los cambios sin guardar?')) event.preventDefault();
    });
})();
