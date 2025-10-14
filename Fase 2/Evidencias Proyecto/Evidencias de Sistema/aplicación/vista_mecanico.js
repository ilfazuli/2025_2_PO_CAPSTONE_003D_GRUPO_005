document.addEventListener('DOMContentLoaded', () => {
  const toast = document.getElementById('asignacionToast');

  setTimeout(() => { if (toast) toast.hidden = false; }, 800);
  toast?.querySelector('[data-toast-dismiss]')?.addEventListener('click', () => {
    toast.hidden = true;
  });

  document.querySelectorAll('.btn').forEach(btn => {
    btn.setAttribute('data-ripple', '1');
    btn.addEventListener('click', (e) => {
      const r = document.createElement('span');
      const rect = btn.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      r.className = 'ripple';
      r.style.width = r.style.height = `${size}px`;
      r.style.left = `${e.clientX - rect.left - size/2}px`;
      r.style.top  = `${e.clientY - rect.top - size/2}px`;
      btn.querySelectorAll('.ripple').forEach(x => x.remove());
      btn.appendChild(r);
      r.addEventListener('animationend', () => r.remove());
    });
  });

  const filtroTexto  = document.getElementById('filtroTexto');
  const filtroEstado = document.getElementById('filtroEstado');
  const lista        = document.getElementById('listaOT');

  function normaliza(s){ return (s||'').toString().toLowerCase(); }

  function aplicaFiltros(){
    const txt = normaliza(filtroTexto?.value);
    const est = (filtroEstado?.value || '').toLowerCase();

    lista.querySelectorAll('.ot-card').forEach(card => {
      const id   = normaliza(card.dataset.id);
      const head = normaliza(card.querySelector('.ot-head').innerText);
      const okTxt = !txt || id.includes(txt) || head.includes(txt);
      const okEst = !est || (card.dataset.estado === est);
      card.style.display = (okTxt && okEst) ? '' : 'none';
    });
  }
  filtroTexto?.addEventListener('input', aplicaFiltros);
  filtroEstado?.addEventListener('change', aplicaFiltros);

  lista.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;

    const card   = btn.closest('.ot-card');
    const otId   = card?.dataset.id;
    const action = btn.dataset.action;

    switch(action){
      case 'info':
        alert(`(Demo) Info del vehículo de ${otId}`);
        break;
      case 'comentario':
        prompt(`(Demo) Agrega un comentario para ${otId}:`);
        break;
      case 'foto':
        alert('(Demo) Aquí abrirías selector de archivos / cámara');
        break;
      case 'iniciar':
        iniciarTrabajo(card);
        break;
      case 'pausar':
        pausarTrabajo(card);
        break;
      case 'repuesto':
        alert('(Demo) Abrir modal para solicitar repuesto');
        break;
      case 'finalizar':
        if (confirm(`¿Finalizar trabajo de ${otId}?`)){
          finalizarTrabajo(card);
        }
        break;
    }
  });

  function setEstado(card, estado){
    card.dataset.estado = estado;

    const badge = card.querySelector('.badge');
    if (!badge) return;
    badge.className = 'badge';
    if (estado === 'pendiente'){ badge.classList.add('badge-pend');  badge.textContent = 'Pendiente'; }
    if (estado === 'en-curso'){  badge.classList.add('badge-run');   badge.textContent = 'En curso'; }
    if (estado === 'pausado'){   badge.classList.add('badge-pause'); badge.textContent = 'Pausado';  }

    const bIniciar   = card.querySelector('[data-action="iniciar"]');
    const bPausar    = card.querySelector('[data-action="pausar"]');
    const bFinalizar = card.querySelector('[data-action="finalizar"]');

    if (estado === 'pendiente'){
      bIniciar.disabled = false;
      bPausar.disabled = true;
      bFinalizar.disabled = true;
      bIniciar.textContent = 'Iniciar trabajo';
    }
    if (estado === 'en-curso'){
      bIniciar.disabled = true;
      bPausar.disabled = false;
      bFinalizar.disabled = false;
      bIniciar.textContent = 'Iniciar trabajo';
    }
    if (estado === 'pausado'){
      bIniciar.disabled = false;
      bPausar.disabled = true;
      bFinalizar.disabled = true;
      bIniciar.textContent = 'Reanudar';
    }
  }

  function iniciarTrabajo(card){
    setEstado(card, 'en-curso');
    console.log('OT iniciada', card.dataset.id);
  }
  function pausarTrabajo(card){
    setEstado(card, 'pausado');
    console.log('OT pausada', card.dataset.id);
  }
  function finalizarTrabajo(card){
    console.log('OT finalizada', card.dataset.id);
    card.style.opacity = .55;
  }

  aplicaFiltros();
});
