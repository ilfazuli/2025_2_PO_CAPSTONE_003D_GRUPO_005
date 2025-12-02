document.addEventListener('DOMContentLoaded', () => {
  // ----- DOM refs
  const form = document.getElementById('formReserva');
  const titleEl = document.getElementById('cal-title');
  const bodyEl  = document.getElementById('cal-body');
  const prevBtn = document.getElementById('cal-prev');
  const nextBtn = document.getElementById('cal-next');

  const vehiculoSel = document.getElementById('vehiculo_id');
  const motivoSel   = document.getElementById('motivo');

  const hiddenFecha  = document.getElementById('fechaSeleccionada');
  const hiddenInicio = document.getElementById('horaInicio');
  const hiddenFin    = document.getElementById('horaFin');
  const hiddenMotivo = document.getElementById('motivoCode');

  const slotWrap   = document.getElementById('slot-wrap');
  const reservarBtn= document.getElementById('btnReservar');

  if (!form || !titleEl || !bodyEl || !prevBtn || !nextBtn || !slotWrap) return;

  // ----- Utils
  const two = n => String(n).padStart(2,'0');
  const fmtISO = d => `${d.getFullYear()}-${two(d.getMonth()+1)}-${two(d.getDate())}`;
  const sameDay=(a,b)=>a&&b&&a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();

  // Calendario state
  const today = new Date();
  const state = { current:new Date(today.getFullYear(), today.getMonth(), 1), selected:null };

  // Rutas
  const slotsEndpoint = form.dataset.slotsEndpoint;

  // Reglas
  let duracionMin = 60;

  // ----- Calendar render
  function renderCalendar() {
    titleEl.textContent = state.current.toLocaleDateString('es-CL', {month:'long', year:'numeric'}).toUpperCase();
    bodyEl.innerHTML = '';

    const y = state.current.getFullYear();
    const m = state.current.getMonth();
    const first = new Date(y,m,1), firstW = first.getDay();
    const daysIn = new Date(y,m+1,0).getDate();
    const daysPrev = new Date(y,m,0).getDate();

    let day=1, next=1;
    for (let r=0;r<6;r++){
      const tr=document.createElement('tr');
      for (let c=0;c<7;c++){
        const i=r*7+c, td=document.createElement('td');
        if (i<firstW) {
          td.textContent = daysPrev-(firstW-1-i);
          td.className = 'prev';
        } else if (day<=daysIn) {
          const d = new Date(y,m,day);
          td.textContent = day;
          if (sameDay(d,today)) td.classList.add('hoy');
          if (sameDay(d,state.selected)) td.classList.add('sel');
          td.dataset.date = fmtISO(d);
          day++;
        } else {
          td.textContent = next++;
          td.className = 'next';
        }
        tr.appendChild(td);
      }
      bodyEl.appendChild(tr);
    }
  }

  prevBtn.addEventListener('click', () => {
    state.current = new Date(state.current.getFullYear(), state.current.getMonth()-1, 1);
    renderCalendar();
    resetSlots();
  });
  nextBtn.addEventListener('click', () => {
    state.current = new Date(state.current.getFullYear(), state.current.getMonth()+1, 1);
    renderCalendar();
    resetSlots();
  });

  bodyEl.addEventListener('click', (e) => {
    const td = e.target.closest('td'); if (!td) return;
    if (!td.dataset.date) return;
    const [Y,M,D] = td.dataset.date.split('-').map(Number);
    state.selected = new Date(Y,M-1,D);
    hiddenFecha.value = td.dataset.date;

    bodyEl.querySelectorAll('td.sel').forEach(el => el.classList.remove('sel'));
    td.classList.add('sel');

    loadSlotsIfReady();
    checkReady();
  });

  // ----- Slots
  function resetSlots(msg = 'Selecciona fecha y motivo para ver horas disponibles') {
    slotWrap.innerHTML = `<div class="slot-empty">${msg}</div>`;
    hiddenInicio.value = '';
    hiddenFin.value = '';
    slotWrap.dataset.loaded = '0';
  }

  async function loadSlotsIfReady() {
    const fecha = hiddenFecha.value;
    const tsId  = motivoSel.value;
    if (!fecha || !tsId) {
      resetSlots();
      return;
    }
    try {
      slotWrap.innerHTML = `<div class="slot-loading">Cargando horarios…</div>`;
      const url = `${slotsEndpoint}?fecha=${encodeURIComponent(fecha)}&ts_id=${encodeURIComponent(tsId)}`;
      const resp = await fetch(url, { headers: { 'Accept':'application/json' }});
      if (!resp.ok) throw new Error('Error al obtener horarios');
      const data = await resp.json();

      duracionMin = Number(data.duracion || 60);
      renderSlots(data.slots || []);
    } catch (err) {
      console.error(err);
      resetSlots('No fue posible cargar los horarios. Intenta nuevamente.');
    }
  }

  function renderSlots(slots) {
    if (!Array.isArray(slots) || slots.length === 0) {
      resetSlots('No hay horas disponibles para la fecha seleccionada.');
      return;
    }
    slotWrap.innerHTML = '';
    slotWrap.dataset.loaded = '1';

    slots.forEach(hhmm => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'slot-btn';
      btn.textContent = hhmm;
      btn.dataset.time = hhmm;

      btn.addEventListener('click', () => {
        // desmarcar previos y marcar actual
        slotWrap.querySelectorAll('.slot-btn.is-selected').forEach(b => b.classList.remove('is-selected'));
        btn.classList.add('is-selected');

        // hidden hora_inicio / hora_fin
        hiddenInicio.value = hhmm;
        const [h,m] = hhmm.split(':').map(Number);
        const finMin = h*60 + m + duracionMin;
        const fin = `${two(Math.floor(finMin/60))}:${two(finMin%60)}`;
        hiddenFin.value = fin;

        checkReady();
      });

      slotWrap.appendChild(btn);
    });
  }

  // ----- Motivo change
  motivoSel?.addEventListener('change', () => {
    hiddenMotivo.value = motivoSel.value || '';
    const opt = motivoSel.selectedOptions[0];
    duracionMin = Number(opt?.dataset?.duracion || 60);
    loadSlotsIfReady();
    checkReady();
  });

  vehiculoSel?.addEventListener('change', checkReady);

  // ----- Habilitar botón cuando todo esté OK
  function checkReady() {
    const ok = !!vehiculoSel?.value && !!motivoSel?.value && !!hiddenFecha.value && !!hiddenInicio.value;
    reservarBtn.disabled = !ok;
  }

  // Init
  renderCalendar();
  resetSlots();

  // --- Toast
  const toast = document.getElementById('toastOk');
  if (toast) {
    // mostrar
    requestAnimationFrame(() => toast.classList.add('res-toast--show'));

    // autocerrar
    const timer = setTimeout(() => {
      toast.classList.remove('res-toast--show');
    }, 8000);

    // cerrar manual
    const btnClose = toast.querySelector('.res-toast__close');
    btnClose?.addEventListener('click', () => {
      clearTimeout(timer);
      toast.classList.remove('res-toast--show');
    });

    // cerrar
    const onKey = (e) => {
      if (e.key === 'Escape') {
        clearTimeout(timer);
        toast.classList.remove('res-toast--show');
        window.removeEventListener('keydown', onKey);
      }
    };
    window.addEventListener('keydown', onKey, { once: true });
  }
});
