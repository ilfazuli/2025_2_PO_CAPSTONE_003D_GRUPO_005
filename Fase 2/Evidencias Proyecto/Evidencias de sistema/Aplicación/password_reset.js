document.addEventListener('DOMContentLoaded', () => {
  const email = document.getElementById('email');
  const pass  = document.getElementById('password');
  const pass2 = document.getElementById('password_confirmation');
  const btn   = document.getElementById('btnReset');
  const msgEmail = document.querySelector('.msg[data-for="email"]');
  const msg1  = document.querySelector('.msg[data-for="password"]');
  const msg2  = document.querySelector('.msg[data-for="password_confirmation"]');
  const form  = document.getElementById('resetForm');
  const toggle= document.querySelector('.pass-wrap .toggle');
  const done  = document.getElementById('doneBox'); 

  if (!form || !email || !pass || !pass2 || !btn) return;

  const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const strong  = (p) => typeof p === 'string'
    && p.length >= 8
    && /[A-ZÁÉÍÓÚÑ]/.test(p)
    && /[a-záéíóúñ]/.test(p)
    && /\d/.test(p)
    && /[!@#$%^&*()[\]{}_\-+=~`|:;"'<>,.?/\\]/.test(p);

  function vEmail(){
    const v = (email.value||'').trim();
    let err = '';
    if (!v) err = 'Ingresa tu correo';
    else if (!reEmail.test(v)) err = 'Correo inválido';
    msgEmail.textContent = err;
    email.classList.toggle('input-error', !!err);
    return !err;
  }

  function vPass(){
    const ok = strong(pass.value||'');
    msg1.textContent = ok ? '' : 'Mínimo 8, mayúscula, minúscula, número y símbolo';
    pass.classList.toggle('input-error', !ok);
    return ok;
  }

  function vConfirm(){
    const ok = (pass2.value||'') === (pass.value||'');
    msg2.textContent = ok ? '' : 'Las contraseñas no coinciden';
    pass2.classList.toggle('input-error', !ok);
    return ok;
  }

  function refresh(){
    const ok = vEmail() & vPass() & vConfirm();
    btn.disabled = !ok;
    return ok;
  }

  form.setAttribute('novalidate','true');
  [email, pass, pass2].forEach(el => el.addEventListener('input', refresh));
  refresh();

  if (toggle) {
    toggle.addEventListener('click', () => {
      const t = pass.type === 'password' ? 'text' : 'password';
      pass.type = t; pass2.type = t;
    });
  }

  form.addEventListener('submit', (e) => {
    if (!refresh()) { e.preventDefault(); return; }
    btn.disabled = true;
    if (done) done.classList.toggle('hidden', false);
  });
});
