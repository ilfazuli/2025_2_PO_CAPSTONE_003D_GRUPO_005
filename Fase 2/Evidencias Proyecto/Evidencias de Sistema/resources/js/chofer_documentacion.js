document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.doc-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const rect = btn.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size/2;
      const y = e.clientY - rect.top  - size/2;

      const r = document.createElement('span');
      r.className = 'doc-ripple';
      r.style.width = r.style.height = `${size}px`;
      r.style.left = `${x}px`;
      r.style.top  = `${y}px`;

      btn.querySelectorAll('.doc-ripple').forEach(s => s.remove());
      btn.appendChild(r);
      r.addEventListener('animationend', () => r.remove());
    });
  });
});
