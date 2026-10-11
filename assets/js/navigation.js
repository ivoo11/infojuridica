(() => {
    const button = document.querySelector('.mobile-menu');
    const nav = document.getElementById('mainNav');
    if (!button || !nav) return;

    const setOpen = open => {
        nav.classList.toggle('is-menu-open', open);
        button.setAttribute('aria-expanded', String(open));
    };

    button.addEventListener('click', () => {
        setOpen(button.getAttribute('aria-expanded') !== 'true');
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
            setOpen(false);
            button.focus();
        }
    });

    nav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => setOpen(false));
    });

    window.matchMedia('(max-width: 1050px)').addEventListener('change', event => {
        if (!event.matches) setOpen(false);
    });
})();
