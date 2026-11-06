(() => {
  const panel = document.querySelector('[data-newsletter-send]');
  if (!panel) return;
  const message = panel.querySelector('[data-send-message]');
  const resume = panel.querySelector('[data-send-resume]');
  const bar = panel.querySelector('[data-send-bar]');
  let busy = false, timer;
  const schedule = seconds => { clearTimeout(timer); timer = setTimeout(run, seconds * 1000); };
  async function run() {
    if (busy) return;
    if (document.hidden) { schedule(5); return; }
    busy = true; resume.hidden = true;
    try {
      const body = new URLSearchParams({ csrf: panel.dataset.csrf });
      const response = await fetch(`/admin/newsletter-procesar.php?id=${encodeURIComponent(panel.dataset.edition)}`, { method: 'POST', body, credentials: 'same-origin', headers: { Accept: 'application/json' } });
      if (response.status === 401 || response.status === 403) throw new Error('Volvé a iniciar sesión o recargá la página. El cron sigue con los pendientes.');
      if (!response.ok) throw new Error('No pudimos actualizar el progreso. El cron sigue con los pendientes.');
      const data = await response.json();
      panel.querySelectorAll('[data-count]').forEach(el => { el.textContent = data.counts[el.dataset.count] || 0; });
      bar.max = Math.max(1, data.total); bar.value = data.total - data.counts.queued - data.counts.processing;
      if (!data.counts.queued) {
        message.textContent = data.counts.processing ? 'Hay un correo en proceso. Si este estado persiste, revisalo antes de reintentar.' : data.counts.failed ? 'El envío terminó con correos fallidos que requieren revisión.' : 'El envío terminó. Los correos enviados fueron aceptados por el servidor.';
        if (data.counts.processing) schedule(10);
      } else if (!data.enabled) { message.textContent = 'Los envíos están deshabilitados.'; }
      else if (data.result === 'failed') { message.textContent = 'Falló un envío. Revisá la conexión antes de continuar; ese correo no se reenviará automáticamente.'; resume.hidden = false; }
      else if (!data.quota.daily_remaining) { message.textContent = 'Se alcanzó el límite de 24 horas. El cron retomará los pendientes cuando se libere cupo.'; schedule(300); }
      else if (!data.quota.minute_remaining || data.result === 'minute_limit') { message.textContent = 'Tanda completada. Esperando el siguiente cupo por minuto; el envío continúa automáticamente.'; schedule(data.quota.retry_after); }
      else { message.textContent = 'Enviando la edición. Podés cerrar esta página: el cron retoma los pendientes.'; schedule(data.result === 'busy' ? 3 : 1); }
    } catch (error) { message.textContent = error.message; resume.hidden = false; }
    finally { busy = false; }
  }
  resume.addEventListener('click', run);
  document.addEventListener('visibilitychange', () => { if (!document.hidden) { clearTimeout(timer); run(); } });
  run();
})();
