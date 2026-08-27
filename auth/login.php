<?php
session_start();
$base_path = '../';
require 'db_config.php';

// Already logged in? Go back to the homepage — logging in shouldn't
// dump you into a separate "dashboard mode."
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();

                header("Location: ../index.php");
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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../styles.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<section class="auth-card">
  <h1>Welcome back</h1>
  <p>Log in to continue your mathematical journey.</p>

  <?php if (!empty($success_message)): ?>
    <div class="auth-success"><?php echo htmlspecialchars($success_message); ?></div>
  <?php endif; ?>
  <?php if (!empty($error_message)): ?>
    <div class="auth-error"><?php echo htmlspecialchars($error_message); ?></div>
  <?php endif; ?>

  <form action="login.php" method="POST">
    <div class="auth-field">
      <label for="email">Email address</label>
      <input type="email" id="email" name="email" required
        placeholder="student@university.edu"
        value="<?php echo htmlspecialchars($email); ?>">
      <p id="email-error" class="field-error hidden">Please enter a valid email address.</p>
    </div>

    <div class="auth-field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required placeholder="••••••••">
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Sign In</button>
  </form>

  <p class="auth-switch">Don't have an account? <a href="register.php">Create one</a></p>
</section>

<?php include '../includes/footer.php'; ?>

<script src="validation.js"></script>

</body>
</html>