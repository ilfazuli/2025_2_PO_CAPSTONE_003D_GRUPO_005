document.addEventListener('DOMContentLoaded', () => {
  const form       = document.getElementById('loginForm');
  const email      = document.getElementById('email');
  const password   = document.getElementById('password');
  const btnLogin   = document.getElementById('btnLogin');
  const togglePass = document.querySelector('.toggle-pass');
  const icon = togglePass.querySelector('i');
  const msgEmail   = document.querySelector('.msg[data-for="email"]');
  const msgPass    = document.querySelector('.msg[data-for="password"]');

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function validate() {
    let ok = true;

    if (!email.value.trim()) {
      msgEmail.textContent = 'Ingresa tu correo';
      ok = false;
    } else if (!emailRegex.test(email.value.trim())) {
      msgEmail.textContent = 'Correo inválido';
      ok = false;
    } else {
      msgEmail.textContent = '';
    }

    if (!password.value) {
      msgPass.textContent = 'Ingresa tu contraseña';
      ok = false;
    } else {
      msgPass.textContent = '';
    }

    btnLogin.disabled = !ok;
    return ok;
  }

  email.addEventListener('input', validate);
  password.addEventListener('input', validate);

  togglePass.addEventListener('click', () => {
    const isPass = password.getAttribute('type') === 'password';
    password.setAttribute('type', isPass ? 'text' : 'password');
    icon.classList.toggle('bi-eye-fill');
    icon.classList.toggle('bi-eye-slash-fill');
    password.focus();
  });
  btnLogin.addEventListener('click', (e) => {
    if (btnLogin.disabled) return;
    const rect = btnLogin.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top  - size / 2;

    const r = document.createElement('span');
    r.className = 'btn-ripple';
    r.style.width = r.style.height = `${size}px`;
    r.style.left = `${x}px`;
    r.style.top  = `${y}px`;

    btnLogin.querySelectorAll('.btn-ripple').forEach(el => el.remove());
    btnLogin.appendChild(r);
    r.addEventListener('animationend', () => r.remove());
  });
  validate();
});
