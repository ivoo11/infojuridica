(() => {
    const dateElement = document.getElementById('argentinaClock');
    if (!dateElement) return;
    const serverTime = Number(dateElement.dataset.timestamp);
    const startedAt = Date.now();
    const formatter = new Intl.DateTimeFormat('es-AR', {
        timeZone: 'America/Argentina/Buenos_Aires', weekday: 'long',
        day: 'numeric', month: 'long', year: 'numeric'
    });
    const dayLength = 86400000;
    const argentinaOffset = -3 * 3600000;
    let timer;
    const update = () => {
        const now = new Date(serverTime + Date.now() - startedAt);
        const parts = Object.fromEntries(formatter.formatToParts(now).map(part => [part.type, part.value]));
        const weekday = parts.weekday.charAt(0).toUpperCase() + parts.weekday.slice(1);
        dateElement.textContent = `${weekday} ${parts.day} de ${parts.month} de ${parts.year}`;
        const localDate = new Date(now.getTime() + argentinaOffset);
        dateElement.dateTime = localDate.toISOString().slice(0, 10);
        // Actualizar al cambiar el día en GMT-3, sin un reloj que corra cada segundo.
        clearTimeout(timer);
        const elapsedToday = ((localDate.getTime() % dayLength) + dayLength) % dayLength;
        timer = setTimeout(update, dayLength - elapsedToday);
    };
    update();
    document.addEventListener('visibilitychange', () => { if (!document.hidden) update(); });
})();
