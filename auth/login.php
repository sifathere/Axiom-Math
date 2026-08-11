<?php
session_start();
require 'db_config.php';

// Already logged in? Skip straight to the dashboard
if (isset($_SESSION['user_email'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = "";
$success_message = $_GET['msg'] ?? "";
$email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error_message = "Both fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please provide a valid email address.";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Regenerate the session ID on every login to prevent session fixation
                session_regenerate_id(true);
                $_SESSION['user_email'] = $email;
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();

                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log In | AxiomMath</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          primary: '#2563EB',
          'primary-dark': '#1d4ed8',
          accent: '#38BDF8',
          dark: '#0F172A',
          muted: '#64748B',
          line: '#E2E8F0',
        },
        fontFamily: {
          display: ['"Space Grotesk"', 'sans-serif'],
          sans: ['Inter', 'sans-serif'],
        },
      },
    },
  };
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#F8FAFC] font-sans text-dark antialiased min-h-screen">

<div class="max-w-4xl mx-auto px-6 pt-6">
  <a href="../index.html" class="inline-flex items-center gap-2 font-display font-bold text-lg text-dark hover:text-primary">
    <span class="w-7 h-7 rounded-lg bg-primary text-white flex items-center justify-center font-mono text-sm">∫</span>
    AxiomMath
  </a>
</div>

<div class="max-w-md mx-auto p-6">
  <header class="mb-8 text-center">
    <h1 class="font-display text-3xl font-bold text-dark">Welcome back</h1>
    <p class="text-muted mt-2">Log in to continue your mathematical journey.</p>
  </header>

  <main class="bg-white p-8 rounded-2xl shadow-md border border-line">
    <?php if (!empty($success_message)): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 text-sm">
        <?php echo htmlspecialchars($success_message); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
      <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 text-sm">
        <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="space-y-5">
      <div>
        <label for="email" class="block text-sm font-semibold text-dark mb-1">Email address</label>
        <input type="email" id="email" name="email" required
          class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none"
          placeholder="student@university.edu"
          value="<?php echo htmlspecialchars($email); ?>">
        <p id="email-error" class="text-red-500 text-xs mt-1 hidden">Please enter a valid email address.</p>
      </div>

      <div>
        <label for="password" class="block text-sm font-semibold text-dark mb-1">Password</label>
        <input type="password" id="password" name="password" required
          class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none"
          placeholder="••••••••">
      </div>

      <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2.5 rounded-lg transition">
        Sign In
      </button>
    </form>

    <div class="mt-6 pt-6 border-t border-line text-center text-sm">
      <span class="text-muted">Don't have an account?</span>
      <a href="register.php" class="text-primary font-semibold hover:underline ml-1">Create an account</a>
    </div>
  </main>
</div>

<script src="validation.js"></script>

</body>
</html>