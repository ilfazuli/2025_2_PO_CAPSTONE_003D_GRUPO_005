document.addEventListener('DOMContentLoaded', () => {
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

  const toastArea = $('#toast-area');
  const showToast = (msg, type = 'success') => {
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.textContent = msg;
    toastArea.appendChild(t);
    setTimeout(() => t.remove(), 3000);
  };

  const openModal  = (m) => m?.classList.remove('hidden');
  const closeModal = (m) => m?.classList.add('hidden');

  $$('#modal-vehiculo, #modal-docs').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m); });
  });
  $$('[data-close]').forEach(btn => {
    btn.addEventListener('click', () => closeModal($(btn.getAttribute('data-close'))));
  });

  $('#btn-open-vehiculo')?.addEventListener('click', () => openModal($('#modal-vehiculo')));
  $('#btn-open-docs')?.addEventListener('click',    () => openModal($('#modal-docs')));

  $('#btn-guardar-vehiculo')?.addEventListener('click', () => {
    closeModal($('#modal-vehiculo'));
    showToast('Vehículo agregado (demo).');
  });
  $('#btn-guardar-docs')?.addEventListener('click', () => {
    closeModal($('#modal-docs'));
    showToast('Documentación guardada (demo).');
  });

  const tbody = $('#tbody-usuarios');
  tbody?.addEventListener('click', (e) => {
    const tr = e.target.closest('tr');
    if (!tr || e.target.closest('.icon-btn')) return;
    const was = tr.classList.contains('selected');
    tbody.querySelectorAll('tr.selected').forEach(r => r.classList.remove('selected'));
    if (!was) tr.classList.add('selected');
  });
  tbody?.addEventListener('click', (e) => {
    const btn = e.target.closest('.icon-btn');
    if (!btn) return;
    e.stopPropagation();
    if (btn.classList.contains('danger')) {
      btn.closest('tr')?.remove();
      showToast('Vehículo eliminado.');
    } else if (btn.classList.contains('edit')) {
      openModal($('#modal-vehiculo'));
    }
  });

  const tabla = $('#tabla-usuarios');
  const empty = $('#empty');
  const filtroNombre  = $('#filtro-nombre');
  const filtroNombreM = $('#filtro-nombre-m');
  const applyFilter = () => {
    const q = (filtroNombre?.value || filtroNombreM?.value || '').toLowerCase();
    let visibles = 0;
    tbody?.querySelectorAll('tr').forEach(tr => {
      const patente = tr.children[1]?.textContent.toLowerCase() || '';
      const show = !q || patente.includes(q);
      tr.style.display = show ? '' : 'none';
      if (show) visibles++;
    });
    if (visibles === 0) { tabla.style.display = 'none'; empty.classList.remove('hidden'); }
    else { tabla.style.display = 'table'; empty.classList.add('hidden'); }
  };
  filtroNombre?.addEventListener('input', applyFilter);
  filtroNombreM?.addEventListener('input', applyFilter);
  $('#btn-buscar')?.addEventListener('click', applyFilter);
  $('#btn-buscar-m')?.addEventListener('click', applyFilter);

  const grid   = $('#docs-grid');
  const addBtn = $('#btn-add-doc-row');
  const MAX_FILES = 12;

  const initDocRow = (row) => {
    const fileInput = $('.file-input', row);
    const btnUpload = $('.btn-upload', row);
    const fileName  = $('.filename', row);
    const img       = $('.thumb-img', row);
    const ph        = $('.thumb-placeholder', row);
    const btnRemove = $('.doc-remove', row);

    btnUpload?.addEventListener('click', () => fileInput?.click());
    fileInput?.addEventListener('change', () => {
      const file = fileInput.files?.[0];
      if (!file) return;
      fileName.textContent = file.name;

      const isImage = /image\/(png|jpe?g|gif|webp)/i.test(file.type);
      if (isImage) {
        const url = URL.createObjectURL(file);
        img.src = url; img.classList.remove('hidden'); ph.classList.add('hidden');
        img.onload = () => URL.revokeObjectURL(url);
      } else {
        img.classList.add('hidden'); ph.classList.remove('hidden'); ph.textContent = 'PDF cargado';
      }
    });

    btnRemove?.addEventListener('click', () => {
      const rows = grid.querySelectorAll('.doc-row');
      if (rows.length <= 1) return; 
      row.remove();
      toggleRemoveState();
    });
  };

  const createRow = () => {
    const base  = grid.querySelector('.doc-row');
    const clone = base.cloneNode(true);

    clone.dataset.doc = '';
    $('.doc-name', clone).value = '';
    $('.filename', clone).textContent = 'Ningún archivo seleccionado';
    const file = $('.file-input', clone); if (file) file.value = '';
    const img  = $('.thumb-img', clone);  img?.classList.add('hidden');
    const ph   = $('.thumb-placeholder', clone); ph?.classList.remove('hidden'); if (ph) ph.textContent = 'Vista\nprevia';
    const date = $('.date-input', clone); if (date) date.value = '';

    initDocRow(clone);
    return clone;
  };

  const toggleRemoveState = () => {
    const rows = grid.querySelectorAll('.doc-row');
    rows.forEach(r => { const rm = $('.doc-remove', r); if (rm) rm.disabled = (rows.length <= 1); });
    if (addBtn) addBtn.disabled = (rows.length >= MAX_FILES);
  };

  initDocRow(grid.querySelector('.doc-row'));
  toggleRemoveState();

  addBtn?.addEventListener('click', () => {
    if (grid.querySelectorAll('.doc-row').length >= MAX_FILES) return;
    const row = createRow();
    grid.appendChild(row);
    toggleRemoveState();
    row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  });
});
