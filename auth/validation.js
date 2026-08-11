// Real-time client-side validation for AxiomMath's login/signup forms.
//
// This is a UX layer only — it runs in the browser BEFORE the form is
// submitted, giving instant feedback. It does NOT replace server-side
// validation: register.php and login.php still check everything again
// after submission, since client-side JS can always be bypassed by a
// user disabling it. This file is shared by both pages; each block
// below checks the relevant elements exist before attaching behavior,
// since login.php doesn't have a strength meter or confirm field.

const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// ---- Email format check (runs on both login.php and register.php) ----
const emailInput = document.getElementById('email');
const emailError = document.getElementById('email-error');

if (emailInput) {
  emailInput.addEventListener('input', function () {
    const isValid = EMAIL_REGEX.test(emailInput.value);
    const hasText = emailInput.value.length > 0;

    emailInput.classList.toggle('border-red-500', hasText && !isValid);
    emailInput.classList.toggle('border-green-500', hasText && isValid);

    if (emailError) {
      emailError.classList.toggle('hidden', !hasText || isValid);
    }
  });
}

// ---- Password strength meter (register.php only) ----
const passwordInput = document.getElementById('password');
const strengthMeter = document.getElementById('strength-meter');
const strengthText = document.getElementById('strength-text');

if (passwordInput && strengthMeter && strengthText) {
  passwordInput.addEventListener('input', function () {
    const value = passwordInput.value;
    let score = 0;

    if (value.length >= 8) score++;       // length threshold
    if (/[A-Z]/.test(value)) score++;     // uppercase letter
    if (/[0-9]/.test(value)) score++;     // numeric digit
    if (/[^A-Za-z0-9]/.test(value)) score++; // special character

    if (value.length === 0) {
      strengthMeter.style.width = '0%';
      strengthMeter.className = 'h-full transition-all';
      strengthText.textContent = 'Password strength';
      strengthText.className = 'text-xs text-muted mt-1';
    } else if (score <= 1) {
      strengthMeter.style.width = '25%';
      strengthMeter.className = 'h-full bg-red-500 transition-all';
      strengthText.textContent = 'Weak';
      strengthText.className = 'text-xs text-red-500 mt-1 font-semibold';
    } else if (score <= 3) {
      strengthMeter.style.width = '60%';
      strengthMeter.className = 'h-full bg-yellow-500 transition-all';
      strengthText.textContent = 'Moderate';
      strengthText.className = 'text-xs text-yellow-600 mt-1 font-semibold';
    } else {
      strengthMeter.style.width = '100%';
      strengthMeter.className = 'h-full bg-green-500 transition-all';
      strengthText.textContent = 'Strong';
      strengthText.className = 'text-xs text-green-600 mt-1 font-semibold';
    }
  });
}

// ---- Confirm password match (register.php only) ----
const confirmInput = document.getElementById('confirm_password');
const matchError = document.getElementById('match-error');

if (confirmInput && passwordInput && matchError) {
  confirmInput.addEventListener('input', function () {
    const hasText = confirmInput.value.length > 0;
    const matches = confirmInput.value === passwordInput.value;

    matchError.classList.toggle('hidden', !hasText || matches);
    confirmInput.classList.toggle('border-red-500', hasText && !matches);
    confirmInput.classList.toggle('border-green-500', hasText && matches);
  });
}