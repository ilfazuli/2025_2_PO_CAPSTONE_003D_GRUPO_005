document.addEventListener('DOMContentLoaded', () => {
  const open = document.getElementById('btn-open-modal');
  const close = document.getElementById('btn-close-modal');
  const cancel = document.getElementById('btn-cancelar');
  const modal = document.getElementById('modal-agregar');

  const show = () => modal && modal.classList.remove('hidden');
  const hide = () => modal && modal.classList.add('hidden');

  open && open.addEventListener('click', show);
  close && close.addEventListener('click', hide);
  cancel && cancel.addEventListener('click', hide);

  modal && modal.addEventListener('click', (e) => {
    if (e.target === modal) hide();
  });



  const btn = document.getElementById('btn-profile');
  const menu = document.getElementById('menu-profile');

  const openMenu = () => {
    menu.classList.remove('hidden');
    btn.setAttribute('aria-expanded', 'true');
  };
  const closeMenu = () => {
    menu.classList.add('hidden');
    btn.setAttribute('aria-expanded', 'false');
  };

  btn && btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const expanded = btn.getAttribute('aria-expanded') === 'true';
    expanded ? closeMenu() : openMenu();
  });

  document.addEventListener('click', (e) => {
    if (!menu || menu.classList.contains('hidden')) return;
    if (!menu.contains(e.target) && !btn.contains(e.target)) closeMenu();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });
});