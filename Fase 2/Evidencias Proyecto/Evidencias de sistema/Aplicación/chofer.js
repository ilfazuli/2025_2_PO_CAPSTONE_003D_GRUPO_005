document.addEventListener('DOMContentLoaded', () => {
  const botones = document.querySelectorAll('.home-opcion a');

  botones.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const rect = btn.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size/2;
      const y = e.clientY - rect.top - size/2;

      const ripple = document.createElement('span');
      ripple.className = 'ripple';
      ripple.style.width = ripple.style.height = `${size}px`;
      ripple.style.left = `${x}px`;
      ripple.style.top  = `${y}px`;

      btn.querySelectorAll('.ripple').forEach(r => r.remove());
      btn.appendChild(ripple);
      ripple.addEventListener('animationend', () => ripple.remove());
    });

    btn.setAttribute('tabindex','0');
    btn.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); btn.click(); }
    });
  });
});
