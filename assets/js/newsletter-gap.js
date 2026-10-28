(() => {
  const slot = document.querySelector('[data-newsletter-gap]');
  const layout = slot?.closest('.lead-layout');
  const lead = layout?.querySelector('.lead-story');
  const signup = document.querySelector('.newsletter-signup');
  if (!slot || !lead || !signup) return;
  const home = document.createComment('newsletter-footer-position');
  signup.before(home);
  const cards = [...layout.querySelectorAll('.side-card')];
  const last = cards.at(-1);
  let queued = false;
  function restore() {
    slot.classList.remove('is-filled');
    if (signup.parentNode === slot) home.after(signup);
  }
  function fit() {
    queued = false;
    if (!last || innerWidth <= 1050) { restore(); return; }
    const top = last.getBoundingClientRect().bottom + 18;
    const bottom = lead.getBoundingClientRect().bottom;
    const available = bottom - top;
    if (available < 220) { restore(); return; }
    slot.style.top = `${top - slot.parentElement.getBoundingClientRect().top}px`;
    if (signup.parentNode !== slot) slot.append(signup);
    if (signup.getBoundingClientRect().height <= available) slot.classList.add('is-filled');
    else restore();
  }
  function schedule() { if (!queued) { queued = true; requestAnimationFrame(fit); } }
  const observer = new ResizeObserver(schedule);
  observer.observe(lead);
  new MutationObserver(schedule).observe(signup, { childList: true, subtree: true, characterData: true });
  cards.forEach(card => observer.observe(card));
  window.addEventListener('resize', schedule);
  document.fonts?.ready.then(schedule);
  window.addEventListener('load', schedule);
  schedule();
})();
