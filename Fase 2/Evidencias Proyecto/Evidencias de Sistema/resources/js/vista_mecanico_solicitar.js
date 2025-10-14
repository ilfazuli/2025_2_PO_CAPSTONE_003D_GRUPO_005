document.addEventListener('DOMContentLoaded', () => {
  const frm   = document.getElementById('frmRepuesto');
  const rep   = document.getElementById('repuesto');
  const cant  = document.getElementById('cantidad');
  const mec   = document.getElementById('mecanico_id');
  const btn   = document.getElementById('btnEnviar');

  const mRep  = document.querySelector('.msg[data-for="repuesto"]');
  const mCant = document.querySelector('.msg[data-for="cantidad"]');
  const mMec  = document.querySelector('.msg[data-for="mecanico_id"]');

  function notEmpty(s){ return (s||'').trim().length > 2; }
  function validCantidad(v){
    const n = Number(v);
    return Number.isInteger(n) && n >= 1 && n <= 9999;
  }

  function check(){
    const okRep  = notEmpty(rep.value);
    const okCant = validCantidad(cant.value);
    const okMec  = !!mec.value;

    mRep.textContent  = okRep  ? '' : 'Describe el repuesto (mín. 3 caracteres).';
    mCant.textContent = okCant ? '' : 'Cantidad entre 1 y 9999.';
    mMec.textContent  = okMec  ? '' : 'Selecciona el mecánico solicitante.';

    btn.disabled = !(okRep && okCant && okMec);
  }

  rep.addEventListener('input', check);
  cant.addEventListener('input', check);
  mec.addEventListener('change', check);

  btn.addEventListener('click', (e) => {
    if (btn.disabled) return;
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

  frm.addEventListener('submit', () => {
    btn.disabled = true;
    btn.textContent = 'Enviando…';
  });

  check();
});
