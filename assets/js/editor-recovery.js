(() => {
  const form = document.querySelector('form[data-recovery-key]');
  if (!form) return;
  const key = form.dataset.recoveryKey;
  const storageKey = `ij-editor-${form.dataset.userId}-${key}`;
  const endpoint = `/admin/editor-respaldo.php?key=${encodeURIComponent(key)}`;
  const status = document.getElementById('editorSaveStatus');
  const panel = document.getElementById('editorRecovery');
  const actions = document.getElementById('editorRecoveryActions');
  const login = document.getElementById('editorLogin');
  const csrf = form.querySelector('[name=csrf]');
  const fields = ['type','title','summary','body','category_id','author_id','image_alt','image_caption','slug','in_context','published_at','backdated_at','document_label','remove_document'];
  let dirty = false, pending = null, restoring = false, sending = false, bypass = false, timer, lastServer = "", submitting = false, saveWait = Promise.resolve(), finishSave;
  if (key.startsWith('new-')) history.replaceState(null, '', `/admin/editar.php?recovery=${key.slice(4)}`);
  function message(text, error = false) { status.textContent = text; panel.classList.toggle('is-error', error); }
  function snapshot() {
    const values = {};
    fields.forEach(name => { const el = form.elements.namedItem(name); if (el) values[name] = el.type === 'checkbox' ? (el.checked ? '1' : '') : el.value; });
    return { fields: values, at: Date.now(), files: [...form.querySelectorAll('[type=file]')].some(el => el.files.length) };
  }
  function localSave() {
    if (!dirty || pending || restoring) return false;
    try { localStorage.setItem(storageKey, JSON.stringify(snapshot())); return true; }
    catch (e) { message('No pudimos respaldar en este dispositivo. Guardá el borrador cuando tengas conexión.', true); return false; }
  }
  function expired() {
    login.hidden = false;
    message('La sesión venció. El texto queda respaldado en este dispositivo. Iniciá sesión para recuperarlo. Las imágenes y los PDF sin guardar deberán seleccionarse de nuevo.', true);
  }
  async function serverSave() {
    if (!dirty || pending || sending || submitting || document.hidden) return;
    const payload = snapshot();
    const signature = JSON.stringify([payload.fields, payload.files]);
    if (signature === lastServer) return;
    const localOK = localSave();
    sending = true; saveWait = new Promise(resolve => { finishSave = resolve; });
    try {
      const data = new FormData(); data.set('csrf', csrf.value); data.set('payload', JSON.stringify(payload));
      const response = await fetch(endpoint, { method: 'POST', body: data, credentials: 'same-origin' });
      if (response.status === 401 || response.status === 403) { expired(); return; }
      if (!response.ok) throw new Error('save');
      lastServer = signature;
      message('Texto respaldado en el servidor. No se publicó ningún cambio.' + (snapshot().files ? ' Para conservar las imágenes o el PDF seleccionados, guardá la nota.' : ''));
    } catch (e) { message(localOK ? 'Sin conexión al servidor. El texto queda respaldado en este dispositivo.' : 'No se pudo respaldar. Guardá el borrador en cuanto sea posible.', !localOK); }
    finally { sending = false; finishSave(); }
  }
  function changed() {
    if (restoring) return;
    dirty = true;
    if (pending) return;
    if (localSave()) message('Texto respaldado en este dispositivo.' + (snapshot().files ? ' Las imágenes y los PDF se conservan al guardar la nota.' : ''));
    clearTimeout(timer); timer = setTimeout(serverSave, 1500);
  }
  function candidate(value) { return value && value.fields && typeof value.fields === 'object' && Number.isFinite(value.at) && value.at > Date.now() - 7 * 86400000; }
  let local = null;
  try { const saved = JSON.parse(localStorage.getItem(storageKey)); if (candidate(saved)) local = saved; else localStorage.removeItem(storageKey); } catch (e) {}
  function offer(saved) {
    if (!candidate(saved)) return;
    const current = snapshot().fields;
    if (JSON.stringify(current) === JSON.stringify(saved.fields)) return;
    pending = saved; actions.hidden = false;
    message('Hay texto sin guardar de una sesión anterior. Podés recuperarlo o conservar la versión guardada.' + (saved.files ? ' Volvé a seleccionar las imágenes o el PDF que no llegaste a guardar.' : ''));
  }
  offer(local);
  fetch(endpoint, { credentials: 'same-origin' }).then(async response => {
    if (response.status === 401) { expired(); return; }
    if (!response.ok) return;
    const result = await response.json(); csrf.value = result.csrf;
    if (!dirty && candidate(result.recovery) && (!local || result.recovery.at > local.at)) offer(result.recovery);
  }).catch(() => {});
  document.getElementById('restoreEditor').addEventListener('click', () => {
    if (!pending || (dirty && !confirm('¿Reemplazar lo que escribiste ahora por el texto respaldado?'))) return;
    const saved = pending; pending = null; restoring = true;
    fields.forEach(name => { const el = form.elements.namedItem(name); if (el && typeof saved.fields[name] === 'string') { if (el.type === 'checkbox') el.checked = saved.fields[name] === '1'; else el.value = saved.fields[name]; } });
    form.elements.namedItem('type').dispatchEvent(new Event('change', { bubbles: true }));
    form.elements.namedItem('body').dispatchEvent(new Event('input', { bubbles: true }));
    restoring = false; actions.hidden = true; changed();
  });
  async function discard() {
    pending = null; actions.hidden = true;
    try { localStorage.removeItem(storageKey); } catch (e) {}
    const data = new FormData(); data.set('csrf', csrf.value); data.set('discard', '1');
    try { await fetch(endpoint, { method: 'POST', body: data, credentials: 'same-origin' }); } catch (e) {}
    message('Se conserva la versión guardada de la publicación.');
  }
  document.getElementById('discardEditorRecovery').addEventListener('click', discard);
  document.getElementById('cancelChanges')?.addEventListener('click', event => {
    if (event.defaultPrevented) return;
    dirty = false; pending = null; submitting = true;
    try { localStorage.removeItem(storageKey); } catch (e) {}
    const data = new FormData(); data.set('csrf', csrf.value); data.set('discard', '1');
    navigator.sendBeacon(endpoint, data);
  });
  form.addEventListener('input', changed); form.addEventListener('change', changed);
  setInterval(serverSave, 20000);
  document.addEventListener('visibilitychange', () => { if (document.hidden) localSave(); else if (!pending) serverSave(); });
  window.addEventListener('pagehide', localSave);
  form.addEventListener('submit', async event => {
    if (bypass) return;
    event.preventDefault(); dirty = true; localSave();
    if (pending) { message('Recuperá o descartá el respaldo anterior antes de guardar esta versión.', true); return; }
    submitting = true;
    await saveWait;
    const submitter = event.submitter;
    try {
      const response = await fetch(endpoint, { credentials: 'same-origin' });
      if (response.status === 401 || response.status === 403) { submitting = false; expired(); return; }
      if (!response.ok) throw new Error('session');
      csrf.value = (await response.json()).csrf;
      bypass = true; form.requestSubmit(submitter);
    } catch (e) { submitting = false; message('No pudimos conectar. El texto sigue en este dispositivo; intentá guardar nuevamente.', true); }
  });
})();
