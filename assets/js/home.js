(() => {
    const track = document.getElementById('agendaTrack');
    const previous = document.getElementById('agendaPrevious');
    const next = document.getElementById('agendaNext');
    if (!track || !previous || !next) return;
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
    window.addEventListener('resize', update);
    update();
})();
