<?php
session_start();
$base_path = '../';
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
    ['title' => 'Problems Solved',    'count' => $problems_solved,    'color' => '#2563EB'],
    ['title' => 'Problems Attempted', 'count' => $problems_attempted, 'color' => '#38BDF8'],
    ['title' => 'Days Active',        'count' => $days_active,        'color' => '#16A34A'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Profile | AxiomMath</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../styles.css">
</head>
<body>

<?php include '../includes/nav.php'; ?>

<section class="page-intro">
  <span class="symbol" style="top:20%; left:10%; font-size:32px; color:rgba(255,255,255,0.18);">∫</span>
  <span class="symbol" style="top:58%; left:86%; font-size:26px; color:rgba(255,255,255,0.18); animation-delay:0.8s;">π</span>
  <span class="eyebrow">Your profile</span>
  <h1>Welcome back</h1>
  <p>Signed in as <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
</section>

<section class="container">
  <h2>Your Stats</h2>
  <div class="stat-grid">
    <?php foreach ($dashboardStats as $stat): ?>
      <div class="stat-card">
        <h3><?php echo htmlspecialchars($stat['title']); ?></h3>
        <div class="stat-value"><?php echo htmlspecialchars($stat['count']); ?></div>
        <div class="stat-sub">
          <span class="stat-dot" style="background:<?php echo $stat['color']; ?>;"></span>This week
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <h2>Continue Learning</h2>
  <div class="action-grid">
    <a href="../practice.php" class="action-card">
      <div class="action-icon">🎯</div>
      <h3>Practice Problems</h3>
      <p>Work through problems by category and check your answers.</p>
    </a>
    <a href="../solver.php" class="action-card">
      <div class="action-icon">🤖</div>
      <h3>AI Solver</h3>
      <p>Stuck on something? Get a guided hint for your own problem.</p>
    </a>
    <a href="../formulas.php" class="action-card">
      <div class="action-icon">📚</div>
      <h3>Formula Hub</h3>
      <p>Browse the formula library, organized by subject.</p>
    </a>
  </div>

  <div class="session-meta">
    <h3>Session info</h3>
    <ul>
      <li>Session token: <code><?php echo session_id(); ?></code></li>
      <li>Login time: <?php echo date("Y-m-d H:i:s", $_SESSION['login_time']); ?></li>
    </ul>
  </div>
</section>

<?php include '../includes/footer.php'; ?>