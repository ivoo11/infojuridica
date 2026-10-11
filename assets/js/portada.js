(() => {
  const cancel = document.getElementById('cancelPortadaChanges');
  const form = cancel?.closest('form');
  if (!form) return;
  const snapshot = () => JSON.stringify(Array.from(new FormData(form).entries()));
  const initial = snapshot();
  cancel.addEventListener('click', event => {
    if (snapshot() !== initial && !window.confirm('¿Descartar los cambios sin guardar? La portada guardada se conserva.')) event.preventDefault();
  });
})();
