// resources/js/guardia.js

document.addEventListener('DOMContentLoaded', () => {
  const $  = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  const openEl  = el => el && el.classList.remove('hidden');
  const closeEl = el => el && el.classList.add('hidden');

  // ==========================================
  // MODAL: AGREGAR VEHÍCULO (ENTRADA)
  // ==========================================
  (function initModalVehiculo () {
    const modal   = $('#modal-vehiculo');
    const openBtn = $('#btn-open-modal-vehiculo');

    if (!modal || !openBtn) return;

    // Abrir modal
    openBtn.addEventListener('click', () => {
      openEl(modal);
    });

    // Cerrar por botones con data-close="#modal-vehiculo"
    $$('[data-close="#modal-vehiculo"]').forEach(btn => {
      btn.addEventListener('click', () => closeEl(modal));
    });

    // Cerrar al hacer click en el fondo oscuro
    modal.addEventListener('click', e => {
      if (e.target === modal) closeEl(modal);
    });
  })();


  // ==========================================
  // MODAL: REGISTRAR SALIDA
  // ==========================================
  (function initModalSalida () {
    const modal     = $('#modal-salida');
    const form      = $('#form-salida');
    const spanPat   = $('#salida_patente');
    const inputHora = $('#salida_hora');

    if (!modal || !form) return;

    // Abrir modal al hacer click en "Terminar"
    $$('.js-terminar-ingreso').forEach(btn => {
      btn.addEventListener('click', () => {
        const id      = btn.dataset.id;          
        const patente = btn.dataset.patente || '';

        // Setear patente visible
        if (spanPat) spanPat.textContent = patente || '—';

        // Limpiar hora anterior
        if (inputHora) inputHora.value = '';

        // Definir action del form
        form.setAttribute('action', `/guardia/checkin/${id}/salida`);

        openEl(modal);
      });
    });

    // Cerrar por botones con data-close="#modal-salida"
    $$('[data-close="#modal-salida"]').forEach(btn => {
      btn.addEventListener('click', () => closeEl(modal));
    });

    // Cerrar al hacer click en el fondo oscuro
    modal.addEventListener('click', e => {
      if (e.target === modal) closeEl(modal);
    });

    // Pequeña validación antes de enviar
    form.addEventListener('submit', e => {
      if (!inputHora || !inputHora.value) {
        e.preventDefault();
        inputHora?.focus();
      }
    });
  })();


  // ==========================================
  // Botón "Editar" para futuro
  // ==========================================
  (function initEditarIngreso () {
    // De momento no hace nada crítico, sólo un placeholder
    $$('.js-editar-ingreso').forEach(btn => {
      btn.addEventListener('click', () => {
        // Aquí más adelante podríamos abrir otro modal de edición
        console.log('Editar ingreso ID:', btn.dataset.id);
      });
    });
  })();

});
