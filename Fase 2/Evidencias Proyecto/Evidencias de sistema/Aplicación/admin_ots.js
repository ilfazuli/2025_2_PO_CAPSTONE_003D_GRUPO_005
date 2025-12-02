// resources/js/admin_ots.js
import Chart from 'chart.js/auto';
document.addEventListener('DOMContentLoaded', () => {
  const $  = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

  /* ========================================
     TOASTS (REUTILIZABLES)
  ======================================== */
  const toastArea = $('#toast-area');

  const showToast = (msg, type = 'info') => {
    if (!toastArea) {
      alert(msg);
      return;
    }

    const box = document.createElement('div');
    box.className = `toast toast-${type}`;
    box.innerHTML = `<span>${msg}</span>`;
    toastArea.appendChild(box);

    setTimeout(() => {
      box.classList.add('is-hiding');
      setTimeout(() => box.remove(), 250);
    }, 3000);
  };

  /* ========================================
      Selección de filas en la tabla de OT
  ======================================== */
  const tbody = $('#tbody-ots');

  if (tbody) {
    tbody.addEventListener('click', (e) => {
      const tr = e.target.closest('tr');
      if (!tr) return;

      // No interferir con links/botones dentro de la fila
      if (e.target.closest('a, button')) return;

      const already = tr.classList.contains('selected');
      tbody.querySelectorAll('tr.selected').forEach(r => r.classList.remove('selected'));
      if (!already) tr.classList.add('selected');
    });
  }

  /* ========================================
      Filtro rápido por texto (folio/patente)
  ======================================== */
  const inputQ = $('#filtro_q');
  const tabla  = $('#tabla-ots');

  const aplicarFiltroRapido = () => {
    if (!tbody || !inputQ) return;

    const q = inputQ.value.trim().toLowerCase();
    let visibles = 0;

    $$('#tbody-ots tr').forEach(tr => {
      const folio   = (tr.querySelector('.col-folio')?.textContent || '').toLowerCase();
      const patente = (tr.querySelector('.col-patente')?.textContent || '').toLowerCase();

      const match = !q || folio.includes(q) || patente.includes(q);
      tr.style.display = match ? '' : 'none';
      if (match) visibles++;
    });

    if (tabla) {
      tabla.style.opacity = visibles === 0 ? 0.7 : 1;
    }
  };

  if (inputQ) {
    inputQ.addEventListener('input', aplicarFiltroRapido);
    inputQ.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        inputQ.value = '';
        aplicarFiltroRapido();
      }
    });
  }

  /* ========================================
      Copiar folio al portapapeles
  ======================================== */
  if (tbody) {
    tbody.addEventListener('click', async (e) => {
      const cell = e.target.closest('.col-folio');
      if (!cell) return;

      const folio = cell.dataset.folio || cell.textContent.trim();
      if (!folio) return;

      try {
        await navigator.clipboard.writeText(folio);
        cell.classList.add('copied');
        showToast(`Folio ${folio} copiado`, 'success');
        setTimeout(() => cell.classList.remove('copied'), 1200);
      } catch {
        showToast('No se pudo copiar el folio.', 'error');
      }
    });
  }

  /* ========================================
      Botón Exportar a Excel
  ======================================== */
  const btnExport = document.querySelector('[data-click="export-ots"]');

  if (btnExport) {
    btnExport.addEventListener('click', (e) => {
      // Evitar dobles envíos
      if (btnExport.dataset.loading === '1') {
        e.preventDefault();
        return;
      }

      btnExport.dataset.loading = '1';

      const originalText = btnExport.innerHTML;
      btnExport.innerHTML = `
        <span class="spinner"></span>
        <span>Generando Excel…</span>
      `;
      btnExport.classList.add('is-loading');

      // Dejamos que el navegador siga el link normalmente (GET al export)
      // Por si algo falla (navegador bloquea, etc.), restauramos al rato:
      setTimeout(() => {
        btnExport.dataset.loading = '0';
        btnExport.innerHTML = originalText;
        btnExport.classList.remove('is-loading');
      }, 7000);
    });
  }

  /* ========================================
     5) Pequeños detalles visuales
  ======================================== */
  // Hover suave en filas ya está con CSS; aquí solo
  // podemos añadir una clase en focus de los filtros
  $$('.form-filtros-ot .input').forEach(inp => {
    inp.addEventListener('focus', () => {
      inp.closest('.filtro')?.classList.add('is-focused');
    });
    inp.addEventListener('blur', () => {
      inp.closest('.filtro')?.classList.remove('is-focused');
    });
  });
});

// --- al final de DOMContentLoaded ---
/* ==== Gráficos Chart.js ==== */
const estadoCanvas = document.getElementById('estadoChart');
const mecanicoCanvas = document.getElementById('mecanicoChart');

const buildEstadoChart = () => {
  if (!estadoCanvas || typeof OTS_POR_ESTADO === 'undefined') return;

  const labels = OTS_POR_ESTADO.map(i => i.estado);
  const data   = OTS_POR_ESTADO.map(i => Number(i.total));

  // eslint-disable-next-line no-unused-vars
  const estadoChart = new Chart(estadoCanvas.getContext('2d'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Cantidad OT',
        data,
        borderWidth: 1,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: { beginAtZero: true, ticks: { precision: 0 } }
      },
      plugins: { legend: { display: false } }
    }
  });
};

const buildMecanicoChart = () => {
  if (!mecanicoCanvas || typeof OTS_POR_MECANICO === 'undefined') return;

  const labels = OTS_POR_MECANICO.map(i => i.nombre ?? i.mecanico_id ?? 'Sin nombre');
  const data   = OTS_POR_MECANICO.map(i => Number(i.total || i.total_cerradas || i.total));

  // eslint-disable-next-line no-unused-vars
  const mecChart = new Chart(mecanicoCanvas.getContext('2d'), {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        label: 'OT cerradas',
        data,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'right' } }
    }
  });
};
buildEstadoChart();
buildMecanicoChart();