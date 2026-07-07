
const appContainer = document.getElementById('app');
let currentView = 'login'; // Global toggle state: 'login' or 'signup'
if(localStorage.getItem('auth_intent')) {
  currentView = localStorage.getItem('auth_intent');
  localStorage.removeItem('auth_intent'); // Clear cache flag
}

// Regex for standard email verification
const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

function renderApp() {
  if (currentView === 'login') {
    appContainer.innerHTML = getLoginTemplate();
    attachLoginListeners();
  } else {
    appContainer.innerHTML = getSignupTemplate();
    attachSignupListeners();
  }
}
//Login Layout
function getLoginTemplate() {
  return `
    <header class="mb-8 text-center">
      <h1 class="font-display text-3xl font-bold text-dark">Welcome Back</h1>
      <p class="text-muted mt-2">Log in to continue your mathematical journey.</p>
    </header>

    <main class="bg-white p-8 rounded-2xl shadow-md border border-line max-w-lg mx-auto">
      <div id="login-error" class="hidden mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg text-sm text-center"></div>
      
      <form id="login-form" class="space-y-6" novalidate>
        <div>
          <label class="block text-sm font-semibold text-dark mb-1" for="login-email">Email address</label>
          <input type="email" id="login-email" required class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none state-transition" placeholder="student@university.edu">
        </div>

        <div>
          <label class="block text-sm font-semibold text-dark mb-1" for="login-password">Password</label>
          <input type="password" id="login-password" required class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none state-transition" placeholder="••••••••">
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2.5 px-4 rounded-lg state-transition shadow-sm">
          Sign In
        </button>
      </form>

      <div class="mt-6 pt-6 border-t border-line text-center text-sm">
        <span class="text-muted">Don't have an account?</span>
        <button id="switch-to-signup" class="text-primary font-semibold hover:underline ml-1">Create an account</button>
      </div>
    </main>
  `;
}

// Signup Layout (With Password Strength Meter)
function getSignupTemplate() {
  return `
    <header class="mb-8 text-center">
      <h1 class="font-display text-3xl font-bold text-dark">Create your account</h1>
      <p class="text-muted mt-2">Start learning with interactive, guided hints.</p>
    </header>

    <main class="bg-white p-8 rounded-2xl shadow-md border border-line max-w-lg mx-auto">
      <form id="signup-form" class="space-y-5" novalidate>
        <div>
          <label class="block text-sm font-semibold text-dark mb-1" for="signup-email">Email address</label>
          <input type="email" id="signup-email" required class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none state-transition" placeholder="student@university.edu">
          <p id="email-error" class="text-red-500 text-xs mt-1 hidden">Please enter a valid email address.</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-dark mb-1" for="signup-password">Password</label>
          <input type="password" id="signup-password" required class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none state-transition" placeholder="At least 8 characters">
          
          <div class="mt-2 flex gap-1 h-1.5 w-full bg-line rounded-full overflow-hidden">
            <div id="strength-bar" class="h-full w-0 state-transition"></div>
          </div>
          <p id="strength-text" class="text-xs text-muted mt-1">Password must be at least 8 characters.</p>
        </div>

        <div>
          <label class="block text-sm font-semibold text-dark mb-1" for="signup-confirm-password">Confirm Password</label>
          <input type="password" id="signup-confirm-password" required class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none state-transition" placeholder="••••••••">
          <p id="match-error" class="text-red-500 text-xs mt-1 hidden">Passwords do not match.</p>
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2.5 px-4 rounded-lg state-transition shadow-sm mt-2">
          Register Account
        </button>
      </form>

      <div class="mt-6 pt-6 border-t border-line text-center text-sm">
        <span class="text-muted">Already have an account?</span>
        <button id="switch-to-login" class="text-primary font-semibold hover:underline ml-1">Log in</button>
      </div>
    </main>
  `;
}



function attachLoginListeners() {
  document.getElementById('switch-to-signup').addEventListener('click', () => {
    currentView = 'signup';
    renderApp();
  });

  const loginForm = document.getElementById('login-form');
  const errorContainer = document.getElementById('login-error');

  loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    errorContainer.classList.add('hidden');

    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;

    // --------------------------------------------------------
    // DATABASE CONNECTION LAYER (LOGIN)
    // --------------------------------------------------------
    /* WHEN CONNECTING TO DATABASE LATER:
      try {
        const response = await fetch('http://localhost:5000/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email, password })
        });
        const data = await response.json();
        if (response.ok) { renderDashboard(data.user); } 
        else { errorContainer.innerText = data.message; errorContainer.classList.remove('hidden'); }
      } catch (err) { ... }
    */

    // CURRENT LOCAL TESTING INTERACTION:
    const mockUsers = JSON.parse(localStorage.getItem('mock_db_users')) || [];
    const matchedUser = mockUsers.find(user => user.email === email && user.password === password);

    if (matchedUser) {
      renderSuccessDashboard(email);
    } else {
      errorContainer.innerText = "Invalid email or password. Access denied.";
      errorContainer.classList.remove('hidden');
    }
  });
}

function attachSignupListeners() {
  document.getElementById('switch-to-login').addEventListener('click', () => {
    currentView = 'login';
    renderApp();
  });

  const signupForm = document.getElementById('signup-form');
  const emailInput = document.getElementById('signup-email');
  const passwordInput = document.getElementById('signup-password');
  const confirmInput = document.getElementById('signup-confirm-password');

  const emailError = document.getElementById('email-error');
  const matchError = document.getElementById('match-error');
  const strengthBar = document.getElementById('strength-bar');
  const strengthText = document.getElementById('strength-text');

  // Real-time password strength rules checker
  passwordInput.addEventListener('input', () => {
    const val = passwordInput.value;
    let strengthScore = 0;

    if (val.length >= 8) strengthScore++;
    if (/[A-Z]/.test(val)) strengthScore++;
    if (/[0-9]/.test(val)) strengthScore++;
    if (/[^A-Za-z0-9]/.test(val)) strengthScore++;

    // UI Updates dynamically handling structural layout widths
    if (val.length === 0) {
      strengthBar.className = "h-full w-0 state-transition";
      strengthText.innerText = "Password must be at least 8 characters.";
      strengthText.className = "text-xs text-muted mt-1";
    } else if (strengthScore <= 1) {
      strengthBar.className = "h-full w-1/4 bg-red-500 state-transition";
      strengthText.innerText = "Weak (Add mixed cases, numbers, or symbols)";
      strengthText.className = "text-xs text-red-500 mt-1";
    } else if (strengthScore === 2 || strengthScore === 3) {
      strengthBar.className = "h-full w-2/4 bg-yellow-500 state-transition";
      strengthText.innerText = "Medium strength";
      strengthText.className = "text-xs text-yellow-600 mt-1";
    } else if (strengthScore === 4) {
      strengthBar.className = "h-full w-full bg-green-500 state-transition";
      strengthText.innerText = "Strong secure password!";
      strengthText.className = "text-xs text-green-600 mt-1";
    }
  });

  signupForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Reset structural state flags
    emailError.classList.add('hidden');
    matchError.classList.add('hidden');

    const email = emailInput.value.trim();
    const password = passwordInput.value;
    const confirmPassword = confirmInput.value;

    let formIsValid = true;

    if (!EMAIL_REGEX.test(email)) {
      emailError.classList.remove('hidden');
      formIsValid = false;
    }

    if (password.length < 8) {
      formIsValid = false;
    }

    if (password !== confirmPassword) {
      matchError.classList.remove('hidden');
      formIsValid = false;
    }

    if (formIsValid) {
      // --------------------------------------------------------
      // DATABASE CONNECTION LAYER (SIGNUP)
      // --------------------------------------------------------
      /* WHEN CONNECTING TO DATABASE LATER:
        try {
          const response = await fetch('http://localhost:5000/api/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
          });
          if (response.ok) { currentView = 'login'; renderApp(); }
        } catch(err) { ... }
      */

      // CURRENT LOCAL TESTING INTERACTION:
      let mockUsers = JSON.parse(localStorage.getItem('mock_db_users')) || [];
      
      if (mockUsers.some(user => user.email === email)) {
        alert("This email is already registered inside local storage context!");
        return;
      }

      mockUsers.push({ email: email, password: password });
      localStorage.setItem('mock_db_users', JSON.stringify(mockUsers));

      alert("Sign up success! Automatically moving you to your new secure Login gate.");
      currentView = 'login';
      renderApp();
    }
  });
}

// ============================================================
// SIMULATED SYSTEM SUCCESS DASHBOARD
// ============================================================
function renderSuccessDashboard(authenticatedEmail) {
  appContainer.innerHTML = `
    <div class="text-center max-w-2xl mx-auto py-12">
      <div class="inline-flex items-center justify-center h-16 w-16 bg-green-100 text-green-600 rounded-full mb-6 text-2xl font-bold">✓</div>
      <h1 class="font-display text-4xl font-bold text-dark mb-4">Authentication Secure</h1>
      <p class="text-muted text-lg mb-8">Access granted for: <span class="font-mono text-primary font-semibold">${authenticatedEmail}</span></p>
      
      <div class="bg-white border border-line rounded-xl p-6 text-left shadow-sm">
        <h2 class="text-sm font-semibold text-muted uppercase tracking-wider mb-4">AxiomMath Workspace</h2>
        <p class="text-dark">Your persistent state verification layers passed successfully. The application context is now initialized.</p>
      </div>

      <button id="btn-logout" class="mt-8 text-sm text-muted hover:text-dark underline font-medium">
        Log Out Securely
      </button>
    </div>
  `;

  document.getElementById('btn-logout').addEventListener('click', () => {
    currentView = 'login';
    renderApp();
  });
}

// Kickstart initial execution run
renderApp();