document.addEventListener('DOMContentLoaded', () => {
  const q = document.getElementById('q');
  const items = [...document.querySelectorAll('.item')];

  function applyFilter() {
    const v = (q.value || '').trim().toLowerCase();
    items.forEach(it => {
      const vh = it.dataset.vehiculo.toLowerCase();
      const pt = it.dataset.patente.toLowerCase();
      it.style.display = (vh.includes(v) || pt.includes(v)) ? '' : 'none';
    });
  }
  q?.addEventListener('input', applyFilter);

  const dlg = document.getElementById('dlgOT');
  const dlgId = document.getElementById('dlgId');
  const closeBtn = document.getElementById('dlgClose');

  document.querySelectorAll('.ver').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const rect = btn.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size/2;
      const y = e.clientY - rect.top - size/2;
      const span = document.createElement('span');
      span.className='ripple';
      span.style.width = span.style.height = `${size}px`;
      span.style.left = `${x}px`;
      span.style.top  = `${y}px`;
      btn.querySelectorAll('.ripple').forEach(r => r.remove());
      btn.appendChild(span);
      span.addEventListener('animationend', () => span.remove());

      dlgId.textContent = btn.dataset.id || '—';
      dlg.showModal();
    });
  });

  closeBtn?.addEventListener('click', () => dlg.close());
});
