(() => {
  const cancel = document.getElementById('cancelAhoraChanges');
  const form = cancel?.closest('form');
  if (!form) return;
  const snapshot = () => JSON.stringify(Array.from(new FormData(form).entries()));
  const initialValues = snapshot();
  cancel.addEventListener('click', event => {
    if (snapshot() !== initialValues && !window.confirm('¿Descartar los cambios sin guardar? El aviso guardado se conserva.')) {
      event.preventDefault();
    }
  });
})();
