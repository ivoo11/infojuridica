(() => {
  const cancel = document.getElementById('cancelBannerChanges');
  const form = cancel?.closest('form');
  if (!form) return;
  const snapshot = () => JSON.stringify([...new FormData(form)]);
  const initial = snapshot();
  let saving = false;
  form.addEventListener('submit', () => { saving = true; });
  cancel.addEventListener('click', event => {
    if (snapshot() !== initial && !confirm('¿Descartar los cambios sin guardar? El banner guardado se conserva.')) event.preventDefault();
    else saving = true;
  });
  window.addEventListener('beforeunload', event => {
    if (!saving && snapshot() !== initial) { event.preventDefault(); event.returnValue = ''; }
  });
})();
