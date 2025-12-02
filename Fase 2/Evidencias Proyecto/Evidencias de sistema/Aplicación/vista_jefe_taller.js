document.addEventListener('DOMContentLoaded', () => {
  // ----- Refs
  const listaAgenda    = document.getElementById('listaAgenda');
  const kpiAbiertas    = document.getElementById('kpiAbiertas');
  const kpiEjecucion   = document.getElementById('kpiEjecucion');
  const kpiFin         = document.getElementById('kpiFinalizadas');
  const tblOTsBody     = document.getElementById('tblOTs')?.querySelector('tbody');
  const filtroPatente  = document.getElementById('filtroPatente');
  const filtroEstado   = document.getElementById('filtroEstado');
  const btnRefrescar   = document.getElementById('btnRefrescar');

  const dlg               = document.getElementById('dlgAsignar');
  const dlgOtId           = document.getElementById('dlgOtId');
  const dlgMecanico       = document.getElementById('dlgMecanico');
  const btnAsignarConfirm = document.getElementById('btnAsignarConfirm');

  // CSRF robusto
  const csrfMeta  = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

  let OTs       = [];
  let AGENDA    = [];
  let MECANICOS = [];

  // ----- Render tabla OTs
  function renderTabla() {
    if (!tblOTsBody) return;

    const term = (filtroPatente?.value || '').toUpperCase();
    const est  = filtroEstado?.value || '';

    const data = OTs.filter(ot =>
      (term === '' || (ot.patente || '').toUpperCase().includes(term)) &&
      (est === ''  || ot.estado_ui === est)
    );

    tblOTsBody.innerHTML = data.map(ot => {
      // Clase visual según estado_ui (pendiente|en_proceso|lista_cierre|finalizada)
      let badgeClass = '';
      switch (ot.estado_ui) {
        case 'en_proceso':
          badgeClass = 'warn';          
          break;
        case 'lista_cierre':           
          badgeClass = 'ready';         
          break;
        case 'finalizada':
          badgeClass = 'ok';
          break;
      }

      // puedeCerrar sólo si backend dice que está lista para cierre 
      const puedeCerrar  = !!ot.ready_to_close;

      // puedeAsignar sólo mientras la OT no está cerrada ni lista para cierre
      const puedeAsignar = (ot.estado_ui === 'pendiente' || ot.estado_ui === 'en_proceso');

      let accionesHtml = '';

      if (puedeAsignar) {
        accionesHtml += `
          <button class="btn-action btn-assign btn-asignar" data-id="${ot.id}" title="Asignar mecánico">
            <i class="bi bi-person-plus"></i>
            <span>Asignar</span>
          </button>
        `;
      }

      if (puedeCerrar) {
        accionesHtml += `
          <button class="btn-action btn-close btn-cerrar" data-id="${ot.id}" title="Cerrar OT">
            <i class="bi bi-check2-circle"></i>
            <span>Cerrar</span>
          </button>
        `;
      }

      if (!accionesHtml) {
        accionesHtml = `<span class="muted">Sin acciones</span>`;
      }

      return `
        <tr>
          <td>${ot.folio}</td>
          <td>${ot.patente}</td>
          <td>${ot.prioridad}</td>
          <td>
            <span class="badge ${badgeClass}">
              ${ot.estado_label}
            </span>
          </td>
          <td>${ot.mecanico ?? '<em>Sin asignar</em>'}</td>
          <td class="cell-actions">
            ${accionesHtml}
          </td>
        </tr>
      `;
    }).join('');
  }

  // ----- Render mecánicos
  function renderMecanicos() {
    if (!dlgMecanico) return;

    if (!MECANICOS.length) {
      dlgMecanico.innerHTML = `<option value="">No hay mecánicos disponibles</option>`;
      return;
    }

    dlgMecanico.innerHTML = MECANICOS.map(m =>
      `<option value="${m.id}">${m.nombre}</option>`
    ).join('');
  }

  // ----- Render agenda
  function renderAgenda() {
    if (!listaAgenda) return;

    if (!AGENDA.length) {
      listaAgenda.innerHTML = `
        <li>
          <span class="muted">No hay agendamientos para hoy.</span>
        </li>
      `;
      return;
    }

    listaAgenda.innerHTML = AGENDA.map(a => `
      <li>
        <span>${a.hora}</span>
        <span>${a.patente}</span>
        <span class="muted">${a.motivo}</span>
      </li>
    `).join('');
  }

  // ----- Cargar datos desde backend
  async function cargarDatos() {
    try {
      const resp = await fetch('/jefe-taller/data');

      if (!resp.ok) {
        const errorText = await resp.text();
        console.error('Respuesta NO OK /jefe-taller/data:', resp.status, errorText);
        throw new Error('Error al cargar datos');
      }

      const data = await resp.json();
      console.log('DATA PANEL JEFE TALLER', data);

      // KPIs
      if (kpiAbiertas)  kpiAbiertas.textContent  = data.kpis?.abiertas ?? '--';
      if (kpiEjecucion) kpiEjecucion.textContent = data.kpis?.ejecucion ?? '--';
      if (kpiFin)       kpiFin.textContent       = data.kpis?.finalizadasHoy ?? '--';

      // OTs
      OTs = data.ots || [];
      console.log('OTs:', OTs);
      renderTabla();

      // Agenda
      AGENDA = data.agenda || [];
      console.log('AGENDA:', AGENDA);
      renderAgenda();

      // Mecánicos
      MECANICOS = data.mecanicos || [];
      console.log('MECANICOS:', MECANICOS);
      renderMecanicos();

    } catch (err) {
      console.error('Error en cargarDatos():', err);
      if (listaAgenda) {
        listaAgenda.innerHTML = `
          <li><span class="muted">Error al cargar la agenda.</span></li>
        `;
      }
    }
  }

  // ----- Eventos filtros
  if (filtroPatente) {
    filtroPatente.addEventListener('input', renderTabla);
  }
  if (filtroEstado) {
    filtroEstado.addEventListener('change', renderTabla);
  }

  if (btnRefrescar) {
    btnRefrescar.addEventListener('click', (e) => {
      e.preventDefault();
      cargarDatos();
    });
  }

  // ----- Inicial
  cargarDatos();

  // ----- Delegación de eventos para acciones en la tabla
  document.body.addEventListener('click', async (e) => {
    const btn = e.target.closest('button');
    if (!btn) return;

    const id = btn.dataset.id;
    if (!id) return;

    // Asignar mecánico
    if (btn.classList.contains('btn-asignar')) {
      if (!dlg || !dlgOtId) return;
      dlgOtId.value = id;
      dlg.showModal();
      return;
    }

    // Cerrar OT
    if (btn.classList.contains('btn-cerrar')) {
      if (!confirm('¿Seguro que deseas cerrar esta OT?')) return;

      try {
        const resp = await fetch(`/jefe-taller/ot/${id}/cerrar`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify({}),
        });

        if (!resp.ok) {
          // Intentamos mostrar el mensaje amigable del backend
          try {
            const errJson = await resp.json();
            alert(errJson.error || 'No se pudo cerrar la OT.');
          } catch {
            alert('No se pudo cerrar la OT.');
          }
          return;
        }

        await cargarDatos();
      } catch (err) {
        console.error(err);
        alert('No se pudo cerrar la OT.');
      }
      return;
    }
  });

  // ----- Confirmar asignación de mecánico
  if (btnAsignarConfirm) {
    btnAsignarConfirm.addEventListener('click', async (e) => {
      e.preventDefault();
      if (!dlgOtId || !dlgMecanico) return;

      const otId = dlgOtId.value;
      const mecanicoId = dlgMecanico.value;

      if (!mecanicoId) {
        alert('Selecciona un mecánico.');
        return;
      }

      try {
        const resp = await fetch(`/jefe-taller/ot/${otId}/asignar-mecanico`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify({ mecanico_id: mecanicoId }),
        });
        if (!resp.ok) throw new Error('Error al asignar mecánico');

        dlg.close();
        await cargarDatos();
      } catch (err) {
        console.error(err);
        alert('No se pudo asignar el mecánico.');
      }
    });
  }
});
