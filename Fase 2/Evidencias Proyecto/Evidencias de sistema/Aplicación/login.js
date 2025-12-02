document.addEventListener('DOMContentLoaded', () => {
  const form       = document.getElementById('loginForm');
  const email      = document.getElementById('email');
  const password   = document.getElementById('password');
  const btnLogin   = document.getElementById('btnLogin');
  const togglePass = document.querySelector('.toggle-pass');
  const icon       = togglePass?.querySelector('i');
  const msgEmail   = document.querySelector('.msg[data-for="email"]');
  const msgPass    = document.querySelector('.msg[data-for="password"]');

  // Regex básico: algo@algo.dominio
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  // Si el backend ya dejó un mensaje de error.
  let touchedEmail = !!(msgEmail && msgEmail.textContent.trim().length);
  let touchedPass  = !!(msgPass && msgPass.textContent.trim().length);

  if (touchedEmail) msgEmail.classList.add('is-error');
  if (touchedPass)  msgPass.classList.add('is-error');

  function validateEmail() {
    if (!touchedEmail) return true;

    const value = email.value.trim();
    if (!value) {
      msgEmail.textContent = 'Ingresa tu correo';
      msgEmail.classList.add('is-error');
      return false;
    }
    if (!emailRegex.test(value)) {
      msgEmail.textContent = 'Ingresa un correo válido (ej: usuario@dominio.cl)';
      msgEmail.classList.add('is-error');
      return false;
    }

    msgEmail.textContent = '';
    msgEmail.classList.remove('is-error');
    return true;
  }

  function validatePassword() {
    if (!touchedPass) return true;

    const value = password.value;
    if (!value) {
      msgPass.textContent = 'Ingresa tu contraseña';
      msgPass.classList.add('is-error');
      return false;
    }
    if (value.length < 6) {
      msgPass.textContent = 'La contraseña debe tener al menos 6 caracteres';
      msgPass.classList.add('is-error');
      return false;
    }

    msgPass.textContent = '';
    msgPass.classList.remove('is-error');
    return true;
  }

  function validateForm() {
    const okEmail = validateEmail();
    const okPass  = validatePassword();
    btnLogin.disabled = !(okEmail && okPass);
    return okEmail && okPass;
  }

  // Eventos EMAIL
  email.addEventListener('input', () => {
    if (touchedEmail) validateEmail();
    validateForm();
  });

  email.addEventListener('blur', () => {
    touchedEmail = true;
    validateForm();
  });

  // Eventos PASSWORD
  password.addEventListener('input', () => {
    if (touchedPass) validatePassword();
    validateForm();
  });

  password.addEventListener('blur', () => {
    touchedPass = true;
    validateForm();
  });

  // Mostrar / ocultar contraseña
  togglePass?.addEventListener('click', () => {
    const isPass = password.getAttribute('type') === 'password';
    password.setAttribute('type', isPass ? 'text' : 'password');
    if (icon) {
      icon.classList.toggle('bi-eye-fill');
      icon.classList.toggle('bi-eye-slash-fill');
    }
    password.focus();
  });

  // Efecto ripple del botón + validación al click
  btnLogin.addEventListener('click', (e) => {
    touchedEmail = true;
    touchedPass  = true;

    if (!validateForm()) {
      // Si la validación del front falla, no mandamos el form
      e.preventDefault();
      return;
    }

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

  // Validar una vez al cargar (por si viene email precargado)
  validateForm();
});
