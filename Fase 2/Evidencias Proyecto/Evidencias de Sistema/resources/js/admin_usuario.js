document.addEventListener('DOMContentLoaded', () => {
  const modal     = document.getElementById('modal');
  const openBtn   = document.getElementById('btn-abrir-modal');
  const closeBtn  = document.getElementById('modal-close');
  const cancelBtn = document.getElementById('modal-cancelar');
  const form      = modal?.querySelector('form.form-grid');

  const openModal  = () => modal?.classList.remove('hidden');
  const closeModal = () => modal?.classList.add('hidden');
  openBtn?.addEventListener('click', openModal);
  closeBtn?.addEventListener('click', closeModal);
  cancelBtn?.addEventListener('click', closeModal);
  modal?.addEventListener('click', e => { if (e.target === modal) closeModal(); });

  const pass  = document.getElementById('u_pass');
  const pass2 = document.getElementById('u_pass_confirmation');

  const validatePasswords = () => {
    if (!pass || !pass2) return true;
    pass.setCustomValidity('');
    pass2.setCustomValidity('');
    if ((pass.value || '').length < 8) {
      pass.setCustomValidity('La contraseña debe tener al menos 8 caracteres.');
      return false;
    }
    if (pass.value !== pass2.value) {
      pass2.setCustomValidity('Las contraseñas no coinciden.');
      return false;
    }
    return true;
  };

  [pass, pass2].forEach(i => i?.addEventListener('input', () => {
    pass?.setCustomValidity('');
    pass2?.setCustomValidity('');
  }));

  form?.addEventListener('submit', (e) => {
    if (!form.checkValidity()) {
      e.preventDefault();
      form.reportValidity();
      return;
    }
    if (!validatePasswords()) {
      e.preventDefault();
      form.reportValidity();
      return;
    }
  });
});


document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.getElementById('tbody-usuarios');
  if (!tbody) return;

  tbody.addEventListener('click', (e) => {
    const tr = e.target.closest('tr');
    if (!tr) return;

    if (e.target.closest('.row-actions')) return;

    const wasSelected = tr.classList.contains('selected');
    tbody.querySelectorAll('tr.selected').forEach(r => r.classList.remove('selected'));
    if (!wasSelected) tr.classList.add('selected');
  });

  tbody.querySelectorAll('.row-actions .icon-btn, .row-actions form').forEach(el => {
    el.addEventListener('click', (ev) => ev.stopPropagation());
  });

  tbody.addEventListener('click', (e) => {
    const btn = e.target.closest('.icon-btn');
    if (!btn) return;

    if (btn.classList.contains('view')) {
      const id = btn.dataset.id;
    }
    if (btn.classList.contains('edit')) {
      const id = btn.dataset.id;
    }
  });
});