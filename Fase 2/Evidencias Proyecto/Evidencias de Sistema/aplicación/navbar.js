document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('nvUserBtn');
  const menu = document.getElementById('nvUserMenu');
  if (!btn || !menu) return;

  function closeMenu() {
    menu.classList.remove('show');
    btn.setAttribute('aria-expanded', 'false');
  }
  function openMenu() {
    menu.classList.add('show');
    btn.setAttribute('aria-expanded', 'true');
  }

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    if (menu.classList.contains('show')) closeMenu(); else openMenu();
  });

  document.addEventListener('click', (e) => {
    if (!menu.contains(e.target) && e.target !== btn) closeMenu();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });
});
