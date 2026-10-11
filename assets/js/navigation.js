(() => {
    const button = document.querySelector('.mobile-menu');
    const nav = document.getElementById('mainNav');
    const panel = document.getElementById('searchPanel');
    if (!button || !nav || !panel) return;
    const compact = window.matchMedia('(max-width: 1100px)');
    const setOpen = open => {
        nav.classList.toggle('is-menu-open', open);
        button.setAttribute('aria-expanded', String(open));
        button.setAttribute('aria-label', open ? 'Cerrar secciones' : 'Abrir secciones');
        if (typeof closeSearch === 'function') closeSearch();
        panel.hidden = !(open && compact.matches);
    };
    button.addEventListener('click', () => setOpen(button.getAttribute('aria-expanded') !== 'true'));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
            setOpen(false); button.focus();
        }
    });
    nav.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setOpen(false)));
    compact.addEventListener('change', () => setOpen(false));
})();
