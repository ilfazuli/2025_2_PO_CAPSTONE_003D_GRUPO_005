document.addEventListener('DOMContentLoaded', () => {
  const email = document.getElementById('email');
  const btn   = document.getElementById('btnSend');
  const msgEl = document.querySelector('.msg[data-for="email"]');
  const okBox = document.getElementById('okBox'); 
  const form  = document.getElementById('forgotForm');

  if (!email || !btn || !msgEl || !form) return;

  const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function validateEmail() {
    const v = (email.value || '').trim();
    let ok = true, err = '';
    if (!v) { ok = false; err = 'Ingresa tu correo'; }
    else if (!reEmail.test(v)) { ok = false; err = 'Ingresa un correo válido'; }

    msgEl.textContent = err;
    email.classList.toggle('input-error', !!err);
    btn.disabled = !ok;
    return ok;
  }

  validateEmail();

  email.addEventListener('input', validateEmail);

  form.addEventListener('submit', (e) => {
    if (!validateEmail()) {
      e.preventDefault();
      return;
    }
    btn.disabled = true;
    btn.textContent = 'Enviando...';
  });
});
