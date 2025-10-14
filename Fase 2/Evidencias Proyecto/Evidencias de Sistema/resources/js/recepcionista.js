document.addEventListener('DOMContentLoaded', () => {
  const btnSimular = document.getElementById('btn-simular');
  const inlineAlert = document.getElementById('alert-solicitud');
  const alertAceptar = document.getElementById('alert-aceptar');

  const tabla = document.getElementById('tabla-solicitudes');
  const tbody = document.getElementById('tbody-solicitudes');
  const empty = document.getElementById('empty-state');
  const tpl = document.getElementById('tpl-fila');

  const toastArea = document.getElementById('toast-area');

  const showToast = (msg, type='info') => {
    const box = document.createElement('div');
    box.className = `toast ${type}`;
    box.innerHTML = `<span>🔔</span><span>${msg}</span><button class="close">×</button>`;
    toastArea.appendChild(box);
    const closer = box.querySelector('.close');
    closer.addEventListener('click', () => box.remove());
    setTimeout(() => box.remove(), 4000);
  };

  const ensureTableVisible = () => {
    if (tabla.style.display === 'none') tabla.style.display = 'table';
    if (!empty.classList.contains('hidden')) empty.classList.add('hidden');
  };

  const addRow = (data) => {
    const node = tpl.content.cloneNode(true);
    node.querySelector('.td-patente').textContent = data.patente;
    node.querySelector('.td-chofer').textContent  = data.chofer;
    node.querySelector('.td-hora').textContent    = data.hora;
    node.querySelector('.td-motivo').textContent  = data.motivo;

    const tr = node.querySelector('tr');
    const btnAceptar  = node.querySelector('.btn-aceptar');
    const btnRechazar = node.querySelector('.btn-rechazar');

    btnAceptar.addEventListener('click', () => {
      tr.style.outline = '2px solid #22c55e';
      showToast('Ingreso aceptado. Notificado al Jefe de Taller.', 'success');
      setTimeout(() => tr.remove(), 1200);
    });

    btnRechazar.addEventListener('click', () => {
      tr.style.outline = '2px solid #ef4444';
      showToast('Solicitud rechazada.', 'info');
      setTimeout(() => tr.remove(), 1200);
    });

    tbody.prepend(node);
  };

  btnSimular?.addEventListener('click', () => {
    inlineAlert.classList.remove('hidden');
    showToast('Solicitud de vehículo entrante', 'info');
  });

  alertAceptar?.addEventListener('click', () => {
    inlineAlert.classList.add('hidden');
    ensureTableVisible();
    addRow({
      patente: 'ABCD23',
      chofer:  'Miguel Pérez',
      hora:    new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}),
      motivo:  'Entrega de insumos'
    });
  });
});