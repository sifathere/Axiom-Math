<?php
session_start();
$base_path = '../';
require 'db_config.php';

// If already logged in, no need to register again — send back to the
// homepage, not a separate "dashboard" phase.
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
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
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../styles.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<section class="auth-card">
  <h1>Create your account</h1>
  <p>Start learning with interactive, guided hints.</p>

  <?php if (!empty($error_message)): ?>
    <div class="auth-error"><?php echo htmlspecialchars($error_message); ?></div>
  <?php endif; ?>

  <form action="register.php" method="POST">
    <div class="auth-field">
      <label for="email">Email address</label>
      <input type="email" id="email" name="email" required
        placeholder="student@university.edu"
        value="<?php echo htmlspecialchars($email); ?>">
      <p id="email-error" class="field-error hidden">Please enter a valid email address.</p>
    </div>

    <div class="auth-field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required
        placeholder="At least 8 characters">
      <div class="strength-bar">
        <div id="strength-meter" class="strength-fill"></div>
      </div>
      <p id="strength-text" class="strength-label">Password strength</p>
    </div>

    <div class="auth-field">
      <label for="confirm_password">Confirm password</label>
      <input type="password" id="confirm_password" name="confirm_password" required
        placeholder="Re-enter password">
      <p id="match-error" class="field-error hidden">Passwords do not match.</p>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Register Account</button>
  </form>

  <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
</section>

<?php include '../includes/footer.php'; ?>

<script src="validation.js"></script>

</body>
</html>