document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('registerForm');
  if (!form) return;

  const nombre   = document.getElementById('nombre');
  const email    = document.getElementById('email');
  const tel      = document.getElementById('telefono');
  const patente  = document.getElementById('patente');
  const pass     = document.getElementById('password');
  const pass2    = document.getElementById('password_confirmation');
  const terms    = document.getElementById('terms');
  const btn      = document.getElementById('btnRegister');

  const msg = (field) => document.querySelector(`.msg[data-for="${field}"]`);
  const setErr = (input, field, text) => {
    msg(field).textContent = text || '';
    input.classList.toggle('input-error', !!text);
  };

  const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const reDigits = /^[\s+0-9()-]+$/;
  const strong = (p) => typeof p === 'string'
    && p.length >= 8
    && /[A-ZÁÉÍÓÚÑ]/.test(p)
    && /[a-záéíóúñ]/.test(p)
    && /\d/.test(p)
    && /[!@#$%^&*()[\]{}_\-+=~`|:;"'<>,.?/\\]/.test(p);

  const ruleEls = {
    len:   document.querySelector('.checklist [data-rule="len"]'),
    upper: document.querySelector('.checklist [data-rule="upper"]'),
    lower: document.querySelector('.checklist [data-rule="lower"]'),
    digit: document.querySelector('.checklist [data-rule="digit"]'),
    spec:  document.querySelector('.checklist [data-rule="spec"]'),
  };

  function vNombre() {
    const v = (nombre.value||'').trim();
    let err = '';
    if (!v) err = 'Ingresa tu nombre';
    else if (v.length < 3) err = 'Debe tener al menos 3 caracteres';
    setErr(nombre, 'nombre', err);
    return !err;
  }

  function vEmail() {
    const v = (email.value||'').trim();
    let err = '';
    if (!v) err = 'Ingresa tu correo';
    else if (!reEmail.test(v)) err = 'Correo inválido';
    setErr(email, 'email', err);
    return !err;
  }

  function vTel() {
    const v = (tel.value||'').trim();
    let err = '';
    if (!v) err = 'Ingresa tu teléfono';
    else if (!reDigits.test(v.replace(/\s+/g,''))) err = 'Solo números y símbolos telefónicos';
    else if (v.replace(/\D/g,'').length < 9) err = 'Muy corto (mínimo 9 dígitos)';
    setErr(tel, 'telefono', err);
    return !err;
  }

  function vPatente() {
    const v = (patente.value||'').trim();
    let err = '';
    if (v && v.length < 5) err = 'Patente demasiado corta';
    setErr(patente, 'patente', err);
    return !err;
  }

  function vPass() {
    const v = pass.value || '';
    const ok = {
      len: v.length >= 8,
      upper: /[A-ZÁÉÍÓÚÑ]/.test(v),
      lower: /[a-záéíóúñ]/.test(v),
      digit: /\d/.test(v),
      spec:  /[!@#$%^&*()[\]{}_\-+=~`|:;"'<>,.?/\\]/.test(v),
    };
    for (const k in ruleEls) ruleEls[k].classList.toggle('ok', !!ok[k]);

    let err = '';
    if (!strong(v)) err = 'Contraseña poco segura';
    setErr(pass, 'password', err);
    return !err;
  }

  function vConfirm() {
    const same = (pass2.value||'') === (pass.value||'');
    setErr(pass2, 'password_confirmation', same ? '' : 'No coincide con la contraseña');
    return same;
  }

  function vTerms() {
    const ok = !!terms.checked;
    msg('terms').textContent = ok ? '' : 'Debes aceptar los términos';
    return ok;
  }

  function refresh() {
    const ok = vNombre() & vEmail() & vTel() & vPatente() & vPass() & vConfirm() & vTerms();
    btn.disabled = !ok;
    return ok;
  }

  document.querySelectorAll('.pass-wrap .toggle').forEach(tg => {
    tg.addEventListener('click', () => {
      const wrap = tg.closest('.pass-wrap');
      const input = wrap.querySelector('input');
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  });

  form.setAttribute('novalidate','true');
  [nombre, email, tel, patente, pass, pass2, terms].forEach(el => {
    el.addEventListener('input', refresh);
    el.addEventListener('change', refresh);
  });
  refresh();

  form.addEventListener('submit', (e) => {
    if (!refresh()) { e.preventDefault(); return; }
    btn.disabled = true;
  });
});
