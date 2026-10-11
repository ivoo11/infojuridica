(() => {
  const input = document.getElementById('articleBody');
  const preview = document.getElementById('bodyPreviewContent');
  if (!input || !preview) return;
  const form = input.closest('form');
  const cancel = document.getElementById('cancelChanges');
  const snapshot = () => JSON.stringify(Array.from(new FormData(form), ([key, value]) =>
    [key, value instanceof File ? (value.name ? [value.name, value.size, value.lastModified] : '') : value]
  ));
  const initialValues = snapshot();
  cancel?.addEventListener('click', event => {
    if (snapshot() !== initialValues && !window.confirm('¿Descartar los cambios sin guardar? La versión guardada se conserva.')) {
      event.preventDefault();
    }
  });
  function render() {
    preview.replaceChildren();
    input.value.trim().split(/\r?\n\s*\r?\n/).filter(Boolean).forEach(block => {
      const quote = block.startsWith('> '), heading = block.startsWith('## ');
      const element = document.createElement(quote ? 'blockquote' : heading ? 'h2' : 'p');
      element.textContent = quote ? block.replace(/^> ?/gm, '') : heading ? block.slice(3) : block;
      preview.append(element);
    });
  }
  document.querySelectorAll('[data-format]').forEach(button => button.addEventListener('click', () => {
    const value = input.value;
    let start = input.selectionStart, end = input.selectionEnd;
    const boundary = value.lastIndexOf('\n\n', Math.max(0, start - 1));
    start = boundary < 0 ? 0 : boundary + 2;
    const next = value.indexOf('\n\n', end);
    end = next < 0 ? value.length : next;
    let block = value.slice(start, end).replace(/^(?:> ?|## )/gm, '');
    if (!block.trim()) block = button.dataset.format === 'quote' ? 'Escribí la cita aquí' : 'Escribí el texto aquí';
    if (button.dataset.format === 'quote') block = block.split('\n\n').map(part => '> ' + part).join('\n\n');
    if (button.dataset.format === 'heading') block = '## ' + block;
    input.setRangeText(block, start, end, 'select');
    input.dispatchEvent(new Event('input', { bubbles: true }));
    document.getElementById('bodyPreview').open = true;
    input.focus();
  }));
  function clipboardBody(clipboard) {
    const plain = clipboard.getData('text/plain').replace(/\r\n?/g, '\n');
    const html = clipboard.getData('text/html');
    if (html) {
      // Parse only in a detached document; pasted markup never enters the editor.
      const document = new DOMParser().parseFromString(html, 'text/html');
      const blocks = new Set(['P', 'DIV', 'SECTION', 'ARTICLE', 'LI', 'UL', 'OL', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6', 'BLOCKQUOTE', 'TR']);
      const ignored = new Set(['SCRIPT', 'STYLE', 'NOSCRIPT', 'TEMPLATE', 'HEAD']);
      function read(node) {
        if (node.nodeType === Node.TEXT_NODE) return node.textContent.replace(/\s+/g, ' ');
        if (node.nodeType !== Node.ELEMENT_NODE || ignored.has(node.tagName)) return '';
        if (node.tagName === 'BR') return '\n';
        let text = Array.from(node.childNodes, read).join('');
        if (blocks.has(node.tagName)) text = '\n\n' + text.trim() + '\n\n';
        return text;
      }
      const text = read(document.body).replace(/[^\S\n]*\n[^\S\n]*/g, '\n').replace(/\n{3,}/g, '\n\n').trim();
      if (text) return text;
    }
    // Plain-text sources often separate paragraphs with just one newline.
    // Keep existing blank lines instead of doubling them again.
    return plain.replace(/\n(?:[^\S\n]*\n)*/g, '\n\n');
  }
  input.addEventListener('paste', event => {
    if (!event.clipboardData) return;
    const text = clipboardBody(event.clipboardData);
    if (!text) return;
    event.preventDefault();
    input.setRangeText(text, input.selectionStart, input.selectionEnd, 'end');
    input.dispatchEvent(new Event('input', { bubbles: true }));
  });
  input.addEventListener('input', render);
  render();
})();
