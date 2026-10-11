(() => {
    const track = document.getElementById('agendaTrack');
    const previous = document.getElementById('agendaPrevious');
    const next = document.getElementById('agendaNext');
    if (!track || !previous || !next) return;
    const alignTitles = () => {
        const titles = [...track.querySelectorAll('.agenda-copy h3')];
        titles.forEach(title => { title.style.height = 'auto'; });
        const height = Math.max(0, ...titles.map(title => title.getBoundingClientRect().height));
        titles.forEach(title => { title.style.height = `${height}px`; });
    };
    const update = () => {
        previous.disabled = track.scrollLeft <= 1;
        next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
    };
    const move = direction => {
        const card = track.querySelector('.agenda-item');
        const step = card.getBoundingClientRect().width + parseFloat(getComputedStyle(track).columnGap);
        track.scrollBy({ left: direction * step, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
    };
    previous.addEventListener('click', () => move(-1));
    next.addEventListener('click', () => move(1));
    track.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', () => { alignTitles(); update(); });
    alignTitles();
    if (document.fonts) document.fonts.ready.then(alignTitles);
    update();
})();
