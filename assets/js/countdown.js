(() => {
  document.querySelectorAll('[data-countdown]').forEach(banner => {
    const target = Number(banner.dataset.target);
    const serverTime = Number(banner.dataset.now);
    const started = performance.now();
    if (!Number.isFinite(target) || !Number.isFinite(serverTime)) { banner.remove(); return; }
    let timer;
    const tick = () => {
      const remaining = Math.ceil((target - serverTime - (performance.now() - started)) / 1000);
      if (remaining <= 0) { clearInterval(timer); banner.remove(); return; }
      const values = { days: Math.floor(remaining / 86400), hours: Math.floor(remaining / 3600) % 24, minutes: Math.floor(remaining / 60) % 60, seconds: remaining % 60 };
      banner.querySelectorAll('[data-unit]').forEach(node => { const value = String(values[node.dataset.unit]).padStart(2, '0'); if (node.textContent !== value) node.replaceChildren(...[...value].map(digit => { const span = document.createElement('span'); span.textContent = digit; return span; })); });
    };
    timer = setInterval(tick, 1000); tick();
    document.addEventListener('visibilitychange', tick);
    window.addEventListener('pageshow', tick);
  });
})();
