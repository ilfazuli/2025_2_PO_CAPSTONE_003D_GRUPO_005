document.addEventListener('DOMContentLoaded', () => {
  const $  = (s, c=document) => c.querySelector(s);
  const $$ = (s, c=document) => Array.from(c.querySelectorAll(s));
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

  /* ================= Utilidades ================= */
  const toastArea = $('#toast-area');
  const showToast = (msg, type='success') => {
    if (!toastArea) return alert(msg);
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.textContent = msg;
    toastArea.appendChild(t);
    setTimeout(() => t.remove(), 3000);
  };

  const openModal  = (m) => m?.classList.remove('hidden');
  const closeModal = (m) => m?.classList.add('hidden');

  // Normaliza texto
  const norm = (txt='') =>
    String(txt)
      .toLowerCase()
      .trim()
      .normalize('NFD')
      .replace(/\p{Diacritic}/gu, '')
      .replace(/\s+/g, '_');

  /* ==============  Selección de filas ============== */
  const tbody = $('#tbody-vehiculos');
  const btnDocs = $('#btn-open-docs');
  let selectedVehiculoId = null;
  let selectedVehiculoPatente = '';

  if (tbody) {
    tbody.addEventListener('click', (e) => {
      const tr = e.target.closest('tr');
      if (!tr) return;

      // No cambiar selección cuando se hace click en iconos de acción
      if (e.target.closest('.icon-btn')) return;

      const was = tr.classList.contains('selected');
      tbody.querySelectorAll('tr.selected').forEach(r => r.classList.remove('selected'));

      if (!was) {
        tr.classList.add('selected');
        selectedVehiculoId      = tr.dataset.id || null;
        selectedVehiculoPatente = (tr.children[1]?.textContent || '').trim();
      } else {
        selectedVehiculoId      = null;
        selectedVehiculoPatente = '';
      }

      if (btnDocs) {
        btnDocs.disabled = !selectedVehiculoId;
      }
    });
  }

  /* ==============  Eliminar vehículo ============== */
  $$('#tbody-vehiculos .js-del').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      const patente = btn.dataset.patente || '';
      if (!confirm(`¿Eliminar el vehículo ${patente}?`)) return;

      try {
        const res = await fetch(`/administrador/vehiculos/${id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        let ok = res.ok;
        try {
          const data = await res.clone().json();
          if (data?.success === false) ok = false;
        } catch {}
        if (!ok) throw new Error();
        btn.closest('tr')?.remove();
        showToast('Vehículo eliminado.');
      } catch {
        showToast('No se pudo eliminar el vehículo.', 'error');
      }
    });
  });

  /* ============== 3) Modal Crear / Editar Vehículo ============== */
  const modal = $('#modal-vehiculo');
  const title = $('#veh-modal-title');
  const form  = $('#form-vehiculo');

  const inpPat = $('#v_patente');
  const inpMar = $('#v_marca');
  const inpMod = $('#v_modelo');
  const inpAn  = $('#v_anio');
  const inpKm  = $('#v_km');
  const selTip = $('#v_tipo');
  const selEst = $('#v_estado');

  let editId = null; 

  // Abrir modal (crear)
  $('#btn-open-vehiculo')?.addEventListener('click', () => {
    editId = null;
    title.textContent = 'Nuevo vehículo';
    form?.reset();
    openModal(modal);
  });

  // Cerrar modal
  $$('#modal-vehiculo [data-close]').forEach(b =>
    b.addEventListener('click', () => closeModal(modal))
  );
  modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(modal); });

  // Abrir modal (editar)
  $$('#tbody-vehiculos .js-edit').forEach(btn => {
    btn.addEventListener('click', () => {
      editId = btn.dataset.id || null;
      title.textContent = 'Editar vehículo';

      inpPat.value = btn.dataset.patente || '';
      inpMar.value = btn.dataset.marca   || '';
      inpMod.value = btn.dataset.modelo  || '';
      inpAn.value  = btn.dataset.anio    || '';
      inpKm.value  = btn.dataset.km      || '';

      const tipoId = btn.dataset.tipoId || '';
      selTip.value = tipoId || '';

      const est = btn.dataset.estado || 'activo';
      selEst.value = est;

      openModal(modal);
    });
  });

  // Guardar (crear/editar)
$('#btn-guardar-vehiculo')?.addEventListener('click', async () => {
    // --- Tomar valores crudos ---
    let patente = inpPat.value.trim();
    const marca = inpMar.value.trim();
    const modelo = inpMod.value.trim();
    const anioRaw = inpAn.value.trim();
    const kmRaw  = inpKm.value.trim();
    const tipoId = selTip.value;
    const estado = selEst.value;

    // --- Normalizar patente ---
    patente = patente.toUpperCase().replace(/\s+/g, '');

    // Formatos aceptados: ABC123 o ABCD23 (también con guión opcional)
    const patenteRegex = /^([A-Z]{3}\d{3}|[A-Z]{4}\d{2})$/;
    if (!patente) {
      showToast('Debe ingresar la patente.','error');
      return;
    }
    if (!patenteRegex.test(patente.replace('-', ''))) {
      showToast('Patente inválida. Ejemplos: ABC123 o ABCD23','error');
      return;
    }

    // --- Validar marca / modelo  ---
    if (marca && marca.length < 2) {
      showToast('La marca debe tener al menos 2 caracteres.','error');
      return;
    }
    if (modelo && modelo.length < 2) {
      showToast('El modelo debe tener al menos 2 caracteres.','error');
      return;
    }

    // --- 4) Validar año  ---
    let anio = null;
    if (anioRaw !== '') {
      anio = Number.parseInt(anioRaw, 10);
      const currentYear = new Date().getFullYear();
      if (Number.isNaN(anio) || anio < 1900 || anio > currentYear + 1) {
        showToast(`El año debe estar entre 1900 y ${currentYear + 1}.`,'error');
        return;
      }
    }

    // --- 5) Validar kilometraje  ---
    let km = null;
    if (kmRaw !== '') {
      km = Number.parseInt(kmRaw, 10);
      if (Number.isNaN(km) || km < 0) {
        showToast('El kilometraje debe ser un número entero mayor o igual a 0.','error');
        return;
      }
    }

    // --- Validar tipo  ---
    if (!tipoId) {
      showToast('Seleccione el tipo de vehículo.','error');
      return;
    }

    // --- Armar payload final ---
    const payload = {
      vehiculo_patente: patente,
      vehiculo_marca:   marca || null,
      vehiculo_modelo:  modelo || null,
      anio:             anio,
      vehiculo_kilometraje_actual: km,
      tipo_vehiculo_id: tipoId,
      estado_vehiculo:  estado || 'activo'
    };

    const isEdit = !!editId;
    const url    = isEdit ? `/administrador/vehiculos/${editId}` : '/administrador/vehiculos';
    const method = isEdit ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF,
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      let ok = res.ok;
      try {
        const data = await res.clone().json();
        if (data?.success === false) ok = false;
      } catch {}

      if (!ok) throw new Error();

      showToast(isEdit ? 'Vehículo actualizado.' : 'Vehículo agregado.');
      closeModal(modal);
      setTimeout(() => location.reload(), 700);
    } catch (e) {
      console.error(e);
      showToast('Error al guardar el vehículo.','error');
    }
  });

  /* ============== 4) Filtros ============== */
  const filtroPat = $('#filtro-patente');
  const tipoSel   = $('#filtro-tipo');
  const estadoSel = $('#filtro-estado');
  const choferSel = $('#filtro-chofer');
  const tabla     = $('#tabla-vehiculos');
  const empty     = $('#empty');

  const cellText = (tr, idx) => (tr.children[idx]?.textContent || '').trim();

  const aplicarFiltros = () => {
    const q       = (filtroPat?.value || '').toLowerCase();
    const tipo    = norm(tipoSel?.value || '');
    const estadoF = norm(estadoSel?.value || '');
    const choferF = (choferSel?.value || '').toLowerCase();

    let visibles = 0;

    tbody?.querySelectorAll('tr').forEach(tr => {
      const patente  = cellText(tr, 1).toLowerCase();
      const tipoTxt  = cellText(tr, 6).toLowerCase();
      const estadoTd = tr.children[7];
      const badge    = estadoTd ? estadoTd.querySelector('.badge') : null;
      const estadoTx = (badge?.textContent || estadoTd?.textContent || '').toLowerCase();
      const choferTx = cellText(tr, 8).toLowerCase();

      const estadoNorm = norm(estadoTx);

      const match =
        (!q      || patente.includes(q)) &&
        (!tipo   || tipoTxt.includes(tipo)) &&
        (!estadoF|| estadoNorm === estadoF) &&
        (!choferF|| choferTx.includes(choferF));

      tr.style.display = match ? '' : 'none';
      if (match) visibles++;
    });

    if (visibles === 0) {
      if (tabla) tabla.style.display = 'none';
      empty?.classList.remove('hidden');
    } else {
      if (tabla) tabla.style.display = 'table';
      empty?.classList.add('hidden');
    }
  };

  $('#btn-buscar')?.addEventListener('click', aplicarFiltros);
  $('#btn-limpiar')?.addEventListener('click', () => {
    if (filtroPat) filtroPat.value = '';
    if (tipoSel)   tipoSel.value   = '';
    if (estadoSel) estadoSel.value = '';
    if (choferSel) choferSel.value = '';
    aplicarFiltros();
  });

  filtroPat?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      aplicarFiltros();
    }
  });

  /* ============== Documentación de vehículo ============== */
  const modalDocs      = $('#modal-documentos');
  const docsVehLabel   = $('#doc-vehiculo-label');
  const docsTbody      = $('#tbody-docs');
  const formDoc        = $('#form-doc');
  const btnDocGuardar  = $('#btn-guardar-doc');
  const btnDocCancelar = $('#btn-doc-cancelar');

  const inpTipo   = $('#doc_tipo');
  const inpEmi    = $('#doc_emision');
  const inpVenc   = $('#doc_vencimiento');
  const inpArch   = $('#doc_archivo');
  const inpDesc   = $('#doc_descripcion');
  const inpDocId  = $('#doc_id');

  let editDocId = null;

  async function cargarDocumentos(vehiculoId) {
    if (!docsTbody) return;
    docsTbody.innerHTML = `
      <tr><td colspan="5" class="text-center muted">Cargando…</td></tr>
    `;

    try {
      const res = await fetch(`/administrador/vehiculos/${vehiculoId}/documentos`, {
        headers: { 'Accept': 'application/json' }
      });
      if (!res.ok) throw new Error();
      const data = await res.json();

      if (!Array.isArray(data) || data.length === 0) {
        docsTbody.innerHTML = `
          <tr><td colspan="5" class="text-center muted">
            No hay documentos cargados para este vehículo.
          </td></tr>
        `;
        return;
      }

      docsTbody.innerHTML = '';
      data.forEach(doc => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${doc.tipo_label || doc.tipo}</td>
          <td>${doc.fecha_emision || '—'}</td>
          <td>${doc.fecha_vencimiento || '—'}</td>
          <td>
            ${doc.archivo_url
              ? `<a href="${doc.archivo_url}" target="_blank" class="link">Ver archivo</a>`
              : '—'
            }
          </td>
          <td>
            <button type="button"
                    class="icon-btn edit js-doc-edit"
                    title="Editar"
                    data-id="${doc.id}"
                    data-tipo="${doc.tipo}"
                    data-emision="${doc.fecha_emision || ''}"
                    data-vencimiento="${doc.fecha_vencimiento || ''}"
                    data-descripcion="${doc.descripcion || ''}">
              <i class="bi bi-pencil-fill"></i>
            </button>
            <button type="button"
                    class="icon-btn danger js-doc-del"
                    title="Eliminar"
                    data-id="${doc.id}">
              <i class="bi bi-trash3-fill"></i>
            </button>
          </td>
        `;
        docsTbody.appendChild(tr);
      });

    } catch {
      docsTbody.innerHTML = `
        <tr><td colspan="5" class="text-center text-error">
          Error al cargar la documentación.
        </td></tr>
      `;
    }
  }

  function resetDocForm() {
    editDocId = null;
    if (!formDoc) return;
    formDoc.reset();
    if (inpDocId)  inpDocId.value = '';
  }

  btnDocs?.addEventListener('click', () => {
    if (!selectedVehiculoId) {
      showToast('Selecciona primero un vehículo en la tabla.','error');
      return;
    }
    resetDocForm();
    if (docsVehLabel) {
      docsVehLabel.textContent = selectedVehiculoPatente || `ID ${selectedVehiculoId}`;
    }
    openModal(modalDocs);
    cargarDocumentos(selectedVehiculoId);
  });

  $$('#modal-documentos [data-close]').forEach(b =>
    b.addEventListener('click', () => closeModal(modalDocs))
  );
  modalDocs?.addEventListener('click', (e) => {
    if (e.target === modalDocs) closeModal(modalDocs);
  });

  btnDocCancelar?.addEventListener('click', resetDocForm);

  // Guardar / actualizar documento
  btnDocGuardar?.addEventListener('click', async () => {
    if (!selectedVehiculoId) {
      showToast('Selecciona un vehículo antes de subir documentos.','error');
      return;
    }
    if (!formDoc) return;

    const formData = new FormData(formDoc);

    let url, method;
    if (editDocId) {
      // Editar
      url = `/administrador/documentos/${editDocId}`;
      method = 'POST';
      formData.append('_method', 'PUT');
    } else {
      // Crear
      url = `/administrador/vehiculos/${selectedVehiculoId}/documentos`;
      method = 'POST';
    }

    try {
      const res = await fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: formData
      });

      if (!res.ok) {
        let errMsg = 'Error al guardar el documento.';
        try {
          const data = await res.json();
          if (data?.message) errMsg = data.message;
        } catch {}
        throw new Error(errMsg);
      }

      showToast(editDocId ? 'Documento actualizado.' : 'Documento guardado.');
      resetDocForm();
      cargarDocumentos(selectedVehiculoId);
    } catch (e) {
      showToast(e.message || 'Error al guardar el documento.','error');
    }
  });

  // Delegar eventos de editar / eliminar en tabla docs
  docsTbody?.addEventListener('click', async (e) => {
    const btnDel  = e.target.closest('.js-doc-del');
    const btnEdit = e.target.closest('.js-doc-edit');

    if (btnDel) {
      const id = btnDel.dataset.id;
      if (!id) return;
      if (!confirm('¿Eliminar este documento?')) return;

      try {
        const res = await fetch(`/administrador/documentos/${id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error();
        showToast('Documento eliminado.');
        cargarDocumentos(selectedVehiculoId);
      } catch {
        showToast('No se pudo eliminar el documento.','error');
      }
    }

    if (btnEdit) {
      editDocId = btnEdit.dataset.id || null;
      if (!editDocId) return;

      if (inpTipo) inpTipo.value = btnEdit.dataset.tipo || '';
      if (inpEmi)  inpEmi.value  = btnEdit.dataset.emision || '';
      if (inpVenc) inpVenc.value = btnEdit.dataset.vencimiento || '';
      if (inpDesc) inpDesc.value = btnEdit.dataset.descripcion || '';
      if (inpDocId) inpDocId.value = editDocId;

      showToast('Editando documento. Al guardar se actualizará.','info');
    }
  });
});
