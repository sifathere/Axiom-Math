<?php
// Shared site header + nav, included by every page.
// The including page must, BEFORE including this file:
//   1. call session_start()
//   2. define $base_path = '' for root pages, or '../' for pages inside auth/
$is_logged_in = isset($_SESSION['user_id']);
?>
<header>
  <nav>
    <a href="<?php echo $base_path; ?>index.php" class="logo"><span class="mark">∫</span>AxiomMath</a>
    <div class="nav-links">
      <a href="<?php echo $base_path; ?>formulas.php">Formula Hub</a>
      <a href="<?php echo $base_path; ?>practice.php">Practice</a>
      <a href="<?php echo $base_path; ?>solver.php">AI Solver</a>
      <a href="<?php echo $base_path; ?>how-it-works.php">How it works</a>
      <a href="<?php echo $base_path; ?>about.php">About</a>
    </div>
    <div class="nav-cta">
      <?php if ($is_logged_in): ?>
        <a href="<?php echo $base_path; ?>auth/dashboard.php" class="profile-icon" title="Your profile">
          <?php echo htmlspecialchars(strtoupper(substr($_SESSION['user_email'], 0, 1))); ?>
        </a>
      <?php else: ?>
        <a href="<?php echo $base_path; ?>auth/login.php" class="btn-login">Log in</a>
        <a href="<?php echo $base_path; ?>auth/register.php" class="btn btn-primary">Sign up free</a>
      <?php endif; ?>
    </div>
  </nav>
</header>