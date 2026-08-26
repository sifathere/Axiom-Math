<?php
session_start();
require 'db_config.php';

// Guard clause — no active session, no access
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Auto-logout after 5 minutes of inactivity
$timeout_duration = 300;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: login.php?msg=" . urlencode("Session timed out due to inactivity"));
    exit();
}
$_SESSION['last_activity'] = time();

// Defensive fallback: sessions created before this feature was added
// won't have user_id stored yet — look it up once if missing.
if (!isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['user_email']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $_SESSION['user_id'] = $row['id'] ?? null;
    $stmt->close();
}
$user_id = $_SESSION['user_id'];

// Real stats pulled from user_progress — a fresh account with no
// attempts yet correctly shows zeros, not fake placeholder numbers.
$solved_stmt = $conn->prepare("SELECT COUNT(*) AS c FROM user_progress WHERE user_id = ? AND is_correct = 1");
$solved_stmt->bind_param("i", $user_id);
$solved_stmt->execute();
$problems_solved = $solved_stmt->get_result()->fetch_assoc()['c'];
$solved_stmt->close();

$attempted_stmt = $conn->prepare("SELECT COUNT(*) AS c FROM user_progress WHERE user_id = ?");
$attempted_stmt->bind_param("i", $user_id);
$attempted_stmt->execute();
$problems_attempted = $attempted_stmt->get_result()->fetch_assoc()['c'];
$attempted_stmt->close();

$active_days_stmt = $conn->prepare("SELECT COUNT(DISTINCT DATE(attempted_at)) AS c FROM user_progress WHERE user_id = ?");
$active_days_stmt->bind_param("i", $user_id);
$active_days_stmt->execute();
$days_active = $active_days_stmt->get_result()->fetch_assoc()['c'];
$active_days_stmt->close();

$dashboardStats = [
    ['title' => 'Problems Solved',    'count' => $problems_solved,    'color' => 'bg-primary'],
    ['title' => 'Problems Attempted', 'count' => $problems_attempted, 'color' => 'bg-accent'],
    ['title' => 'Days Active',        'count' => $days_active,        'color' => 'bg-green-500'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | AxiomMath</title>
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
          mono: ['"JetBrains Mono"', 'monospace'],
        },
      },
    },
  };
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
</head>
<body class="bg-[#F8FAFC] font-sans text-dark antialiased min-h-screen">

<nav class="bg-dark text-white px-6 py-4 flex justify-between items-center shadow-md">
  <a href="../index.html" class="inline-flex items-center gap-2 font-display font-bold text-lg text-white">
    <span class="w-7 h-7 rounded-lg bg-primary text-white flex items-center justify-center font-mono text-sm">∫</span>
    AxiomMath
  </a>
  <div class="flex items-center gap-4">
    <a href="../practice.php" class="text-sm text-gray-300 hover:text-white transition">Practice</a>
    <a href="logout.php" class="bg-red-600 hover:bg-red-500 text-white text-sm font-bold px-4 py-2 rounded-lg transition">
      Log Out
    </a>
  </div>
</nav>

<main class="max-w-4xl mx-auto p-6 mt-8">
  <div class="text-center mb-10">
    <div class="inline-flex items-center justify-center h-16 w-16 bg-green-100 text-green-600 rounded-full mb-6 text-2xl font-bold">✓</div>
    <h1 class="font-display text-3xl font-bold text-dark mb-2">Welcome back!</h1>
    <p class="text-muted">
      Signed in as <span class="font-mono text-primary font-semibold"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
    </p>
  </div>

  <h2 class="text-sm font-semibold text-muted uppercase tracking-wider mb-4">Your Workspace</h2>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
    <?php foreach ($dashboardStats as $stat): ?>
      <div class="bg-white border border-line rounded-xl p-5 shadow-sm">
        <h3 class="text-xs font-semibold text-muted uppercase tracking-wider"><?php echo htmlspecialchars($stat['title']); ?></h3>
        <p class="text-3xl font-display font-bold text-dark mt-2"><?php echo htmlspecialchars($stat['count']); ?></p>
        <div class="flex items-center mt-3">
          <span class="h-2.5 w-2.5 rounded-full <?php echo $stat['color']; ?> mr-2"></span>
          <span class="text-xs text-muted">This week</span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <h2 class="text-sm font-semibold text-muted uppercase tracking-wider mb-4">Continue Learning</h2>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
    <a href="../practice.php" class="block bg-white border border-line rounded-xl p-6 shadow-sm hover:shadow-md hover:border-primary transition">
      <div class="text-2xl mb-3">🎯</div>
      <h3 class="font-display font-bold text-dark mb-1">Practice Problems</h3>
      <p class="text-sm text-muted">Work through problems by category and check your answers.</p>
    </a>
    <a href="../solver.php" class="block bg-white border border-line rounded-xl p-6 shadow-sm hover:shadow-md hover:border-primary transition">
      <div class="text-2xl mb-3">🤖</div>
      <h3 class="font-display font-bold text-dark mb-1">AI Solver</h3>
      <p class="text-sm text-muted">Stuck on something? Get a guided hint for your own problem.</p>
    </a>
    <a href="../formulas.php" class="block bg-white border border-line rounded-xl p-6 shadow-sm hover:shadow-md hover:border-primary transition">
      <div class="text-2xl mb-3">📚</div>
      <h3 class="font-display font-bold text-dark mb-1">Formula Hub</h3>
      <p class="text-sm text-muted">Browse the formula library, organized by subject.</p>
    </a>
  </div>

  <div class="bg-white border border-line rounded-xl p-6 text-left shadow-sm">
    <h3 class="text-sm font-bold text-muted uppercase tracking-wider mb-2">Security Logging Metadata</h3>
    <ul class="text-xs text-muted space-y-1">
      <li>Session Token: <code class="bg-gray-100 px-1 py-0.5 rounded text-primary"><?php echo session_id(); ?></code></li>
      <li>Login Time: <?php echo date("Y-m-d H:i:s", $_SESSION['login_time']); ?></li>
    </ul>
  </div>
</main>

<footer class="bg-dark text-gray-400 mt-16 py-8 px-6">
  <div class="max-w-4xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4 text-sm">
    <span>&copy; 2026 AxiomMath. All rights reserved.</span>
    <div class="flex gap-6">
      <a href="../index.html" class="hover:text-white transition">Home</a>
      <a href="../formulas.php" class="hover:text-white transition">Formula Hub</a>
      <a href="../practice.php" class="hover:text-white transition">Practice</a>
    </div>
  </div>
</footer>

</body>
</html>