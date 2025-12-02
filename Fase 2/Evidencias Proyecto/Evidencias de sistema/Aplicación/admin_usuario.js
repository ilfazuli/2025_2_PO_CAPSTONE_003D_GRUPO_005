// ============== Admin Usuarios  ==============
document.addEventListener('DOMContentLoaded', () => {
  // ---------- utils ----------
  const $  = (sel, ctx=document) => ctx.querySelector(sel);
  const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));
  const openEl  = el => el && el.classList.remove('hidden');
  const closeEl = el => el && el.classList.add('hidden');

  // ======================================================
  // BLOQUE: Modal "Agregar usuario"
  // ======================================================
  (function initCreateModal(){
    const modal     = $('#modal');
    const openBtn   = $('#btn-abrir-modal');
    const closeBtn  = $('#modal-close');
    const cancelBtn = $('#modal-cancelar');
    const form      = modal?.querySelector('form.form-grid');
    const pass      = $('#u_pass');
    const pass2     = $('#u_pass_confirmation');

    openBtn?.addEventListener('click', () => openEl(modal));
    closeBtn?.addEventListener('click', () => closeEl(modal));
    cancelBtn?.addEventListener('click', () => closeEl(modal));
    modal?.addEventListener('click', e => { if (e.target === modal) closeEl(modal); });

    const validatePasswords = () => {
      if (!pass || !pass2) return true;
      pass.setCustomValidity(''); pass2.setCustomValidity('');
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
      pass?.setCustomValidity(''); pass2?.setCustomValidity('');
    }));

    form?.addEventListener('submit', (e) => {
      if (!form.checkValidity() || !validatePasswords()) {
        e.preventDefault();
        form.reportValidity();
      }
    });
  })();

  // ======================================================
  // BLOQUE: Selección de filas de la tabla
  // ======================================================
  (function initRowSelection(){
    const tbody = $('#tbody-usuarios');
    if (!tbody) return;

    tbody.addEventListener('click', (e) => {
      const tr = e.target.closest('tr'); if (!tr) return;
      if (e.target.closest('.row-actions')) return;
      const was = tr.classList.contains('selected');
      tbody.querySelectorAll('tr.selected').forEach(r => r.classList.remove('selected'));
      if (!was) tr.classList.add('selected');
    });

    tbody.addEventListener('click', (ev) => {
      if (ev.target.closest('.row-actions .icon-btn') || ev.target.closest('.row-actions form')) {
        ev.stopPropagation();
      }
    });
  })();

  // ======================================================
  // BLOQUE: Modal "Editar usuario"
  // ======================================================
  (function initEditModal(){
    const editModal  = $('#modal-edit');
    const form       = $('#form-edit');
    if (!editModal || !form) return;

    const selRol = $('#edit_roles');

    $$('.js-open-edit').forEach(btn => {
      btn.addEventListener('click', () => {
        const tr    = btn.closest('tr');
        const id    = btn.dataset.id;
        const nom   = tr.children[1].textContent.trim();
        const mail  = tr.children[3].textContent.trim();
        const tel   = tr.children[4].textContent.trim();
        const roles = tr.dataset.roles.split(',').map(r => r.trim().toUpperCase());

        form.action = `/administrador/usuarios/${id}`;
        $('#edit_nombre').value   = nom;
        $('#edit_email').value    = mail;
        $('#edit_telefono').value = (tel && tel !== '—') ? tel : '';

        if (selRol) {
          Array.from(selRol.options).forEach(o => {
            o.selected = roles.includes(o.text.trim().toUpperCase());
          });
        }

        openEl(editModal);
      });
    });

    $$('[data-close="#modal-edit"]').forEach(b => b.addEventListener('click', () => closeEl(editModal)));
    editModal.addEventListener('click', e => { if (e.target === editModal) closeEl(editModal); });
  })();

  // ======================================================
  // BLOQUE: Modal "Ver usuario"
  // ======================================================
  (function initViewModal(){
    const modalView = $('#modal-view');
    if (!modalView) return;

    $$('.js-open-view').forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = btn.dataset.id;
        try {
          const res = await fetch(`/administrador/usuarios/${id}`);
          if (!res.ok) throw new Error('HTTP '+res.status);
          const data = await res.json();

          $('#view_nombre').textContent   = data.usuario_nombre ?? '—';
          $('#view_email').textContent    = data.usuario_email ?? '—';
          $('#view_telefono').textContent = data.usuario_telefono ?? '—';
          $('#view_roles').textContent    = (data.roles || []).join(', ') || '—';
          $('#view_estado').textContent   = data.usuario_estado ? 'Activo' : 'Inactivo';
          $('#view_creado').textContent   = data.created_at ?? '—';

          openEl(modalView);
        } catch (err) {
          console.error(err);
          alert('No se pudo cargar la información del usuario.');
        }
      });
    });

    $$('[data-close="#modal-view"]').forEach(b => b.addEventListener('click', () => closeEl(modalView)));
    modalView.addEventListener('click', e => { if (e.target === modalView) closeEl(modalView); });
  })();

  // ======================================================
  // BLOQUE: Filtros
  // ======================================================
  (function initFilters(){
    const form    = $('#filtros-form');
    if (!form) return;

    const selRol  = $('#filtro-rol');
    const inpQ    = $('#filtro-nombre');
    const btnClr  = $('#btn-limpiar-filtros');
    const btnGo   = $('#btn-buscar');

    btnGo?.addEventListener('click', () => form.submit());
    btnClr?.addEventListener('click', (e) => {
      e.preventDefault();
      if (selRol) selRol.value = '';
      if (inpQ)   inpQ.value   = '';
      form.submit();
    });
    inpQ?.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        form.submit();
      }
      if (e.key === 'Escape') {
        inpQ.value = '';
      }
    });
  })();

  // ======================================================
  // BLOQUE: Resetear contraseña (modal)
  // ======================================================
  (function initResetPassword(){
    const btnReset   = $('#btn-reset-masivo');
    const modalReset = $('#modal-reset');
    const formReset  = $('#form-reset');
    const pass1      = $('#reset_pass');
    const pass2      = $('#reset_pass_conf');

    if (!btnReset || !modalReset || !formReset) return;

    const getSelectedUserId = () => {
      const tr = $('#tbody-usuarios tr.selected');
      return tr ? tr.getAttribute('data-id') : null;
    };

    const clearValidity = () => [pass1, pass2].forEach(i => i && i.setCustomValidity(''));

    const validateReset = () => {
      clearValidity();
      if ((pass1.value || '').length < 8) {
        pass1.setCustomValidity('La contraseña debe tener al menos 8 caracteres.');
        formReset.reportValidity();
        return false;
      }
      if (pass1.value !== pass2.value) {
        pass2.setCustomValidity('Las contraseñas no coinciden.');
        formReset.reportValidity();
        return false;
      }
      return true;
    };

    pass1?.addEventListener('input', clearValidity);
    pass2?.addEventListener('input', clearValidity);

    btnReset.addEventListener('click', () => {
      const id = getSelectedUserId();
      if (!id) {
        alert('Selecciona un usuario de la tabla para resetear la contraseña.');
        return;
      }
      formReset.setAttribute('action', `/administrador/usuarios/${id}/password`);
      openEl(modalReset);
    });

    $$('[data-close="#modal-reset"]').forEach(btn => btn.addEventListener('click', () => closeEl(modalReset)));
    modalReset.addEventListener('click', e => { if (e.target === modalReset) closeEl(modalReset); });

    formReset.addEventListener('submit', (e) => {
      if (!validateReset()) e.preventDefault();
    });
  })();

  // ======================================================
  // BLOQUE: Confirmación SOLO para eliminar
  // ======================================================
  (function initConfirmDelete(){
    const modal = $('#confirm-modal');
    const title = $('#confirm-title');
    const text  = $('#confirm-text');
    const btnOk = $('#confirm-accept');
    let onAccept = null;

    const openConfirm = (t, msg, submitFn) => {
      title.textContent = t;
      text.textContent  = msg;
      onAccept = submitFn;
      openEl(modal);
    };
    const closeConfirm = () => { onAccept = null; closeEl(modal); };

    $$('[data-close="#confirm-modal"]').forEach(b => b.addEventListener('click', closeConfirm));
    modal?.addEventListener('click', e => { if (e.target === modal) closeConfirm(); });
    btnOk?.addEventListener('click', () => { if (onAccept) onAccept(); closeConfirm(); });

    $$('.js-open-delete').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const nombre = btn.dataset.nombre || 'este usuario';
        const form = document.getElementById(`del-${id}`);
        if (!form) return;

        openConfirm(
          'Eliminar usuario',
          `¿Seguro que deseas eliminar a “${nombre}”? Esta acción no se puede deshacer.`,
          () => form.submit()
        );
      });
    });
  })();

  // ======================================================
  // BLOQUE: Activar/Desactivar (candado)
  // ======================================================
  (function initToggleModal(){
    const modal      = $('#modal-toggle');
    const form       = $('#form-toggle');
    if (!modal || !form) return;

    const title      = $('#toggle-title');
    const infoBox    = $('#toggle-info');
    const passBlock  = $('#toggle_password_block');
    const estadoInp  = $('#toggle_estado');
    const pass1      = $('#toggle_pass');
    const pass2      = $('#toggle_pass_conf');

    const showPwBlock = () => {
      passBlock?.classList.remove('hidden');
      if (pass1) { pass1.required = true; pass1.value = ''; }
      if (pass2) { pass2.required = true; pass2.value = ''; }
    };
    const hidePwBlock = () => {
      passBlock?.classList.add('hidden');
      if (pass1) { pass1.required = false; pass1.value = ''; }
      if (pass2) { pass2.required = false; pass2.value = ''; }
    };

    // abrir modal
    $$('.js-open-toggle').forEach(btn => {
      btn.addEventListener('click', () => {
        const nombre = btn.dataset.nombre || 'el usuario';
        const next   = parseInt(btn.dataset.estadoNext || '0', 10);
        const action = btn.dataset.action;

        form.setAttribute('action', action);
        estadoInp.value = String(next);

        if (next === 1) {
          title.textContent   = 'Reactivar usuario';
          infoBox.textContent = `Vas a reactivar a “${nombre}”. Debes definir una nueva contraseña.`;
          showPwBlock();
        } else {
          title.textContent   = 'Desactivar usuario';
          infoBox.textContent = `Vas a desactivar a “${nombre}”. El usuario no podrá acceder.`;
          hidePwBlock();
        }

        openEl(modal);
      });
    });

    // validación al confirmar
    form.addEventListener('submit', (e) => {
      const next = parseInt(estadoInp.value || '0', 10);
      if (next === 1) {
        pass1.setCustomValidity('');
        pass2.setCustomValidity('');
        if (!pass1?.value || pass1.value.length < 8) {
          pass1.setCustomValidity('Debe tener al menos 8 caracteres.');
          e.preventDefault(); form.reportValidity(); return;
        }
        if (pass1.value !== pass2.value) {
          pass2.setCustomValidity('Las contraseñas no coinciden.');
          e.preventDefault(); form.reportValidity(); return;
        }
      }
    });

    [pass1, pass2].forEach(i => i?.addEventListener('input', () => {
      i.setCustomValidity('');
    }));

    $$('[data-close="#modal-toggle"]').forEach(b => b.addEventListener('click', () => closeEl(modal)));
    modal.addEventListener('click', e => { if (e.target === modal) closeEl(modal); });
  })();
});
