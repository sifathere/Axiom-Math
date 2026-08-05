<?php
session_start();
require 'db_config.php';

// If already logged in, no need to register again
if (isset($_SESSION['user_email'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = "";
$email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please provide a valid email address.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check whether the email is already registered
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "This email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $insert->bind_param("ss", $email, $hashed_password);

            if ($insert->execute()) {
                header("Location: login.php?msg=" . urlencode("Account created. Please log in."));
                exit();
            } else {
                $error_message = "Something went wrong. Please try again.";
            }
            $insert->close();
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
<title>Sign Up | AxiomMath</title>
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
    <h1 class="font-display text-3xl font-bold text-dark">Create your account</h1>
    <p class="text-muted mt-2">Start learning with interactive, guided hints.</p>
  </header>

  <main class="bg-white p-8 rounded-2xl shadow-md border border-line">
    <?php if (!empty($error_message)): ?>
      <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 text-sm">
        <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>

    <form action="register.php" method="POST" class="space-y-5">
      <div>
        <label for="email" class="block text-sm font-semibold text-dark mb-1">Email address</label>
        <input type="email" id="email" name="email" required
          class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none"
          placeholder="student@university.edu"
          value="<?php echo htmlspecialchars($email); ?>">
      </div>

      <div>
        <label for="password" class="block text-sm font-semibold text-dark mb-1">Password</label>
        <input type="password" id="password" name="password" required
          class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none"
          placeholder="At least 8 characters">
      </div>

      <div>
        <label for="confirm_password" class="block text-sm font-semibold text-dark mb-1">Confirm password</label>
        <input type="password" id="confirm_password" name="confirm_password" required
          class="w-full px-4 py-2 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none"
          placeholder="Re-enter password">
      </div>

      <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2.5 rounded-lg transition">
        Register Account
      </button>
    </form>

    <div class="mt-6 pt-6 border-t border-line text-center text-sm">
      <span class="text-muted">Already have an account?</span>
      <a href="login.php" class="text-primary font-semibold hover:underline ml-1">Log in</a>
    </div>
  </main>
</div>

</body>
</html>
