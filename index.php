<?php
session_start();
$base_path = '';
require_once 'auth/db_config.php';

// Pull one real problem for the "Try It Now" live demo — a fixed,
// well-known example so first-time visitors see something recognizable.
$demo_stmt = $conn->query("SELECT question, hint FROM practice_problems ORDER BY problem_id ASC LIMIT 1");
$demo_problem = $demo_stmt ? $demo_stmt->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AxiomMath — Mathematics Made Understandable</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<section class="hero">
  <span class="symbol" style="top:14%; left:6%; font-size:50px;">π</span>
  <span class="symbol" style="top:70%; left:3%; font-size:32px; animation-delay:1.4s;">∞</span>
  <span class="symbol" style="top:8%; left:46%; font-size:26px; animation-delay:2.1s;">√</span>
  <div class="wrap">
    <div class="hero-copy">
      <span class="eyebrow">Built for every learner</span>
      <h1>Mathematics, <span class="accent-text">made&nbsp;understandable.</span></h1>
      <p class="sub">Solve equations, explore formulas, and get guided hints that teach you the next step — instead of just handing you the answer.</p>
      <div class="hero-actions">
        <a href="practice.php" class="btn btn-primary">Start learning →</a>
        <a href="formulas.php" class="btn btn-ghost">Explore formula library</a>
      </div>
    </div>
    <div class="hero-visual">
      <img src="img.jpg" alt="Hand-drawn mathematical formulas on graph paper">
    </div>
  </div>
</section>

<section class="container">
  <h2>What AxiomMath Offers</h2>
  <div class="cards">
    <a href="solver.php" class="card">
      <div style="font-size:26px; margin-bottom:10px;">🤖</div>
      <h3>AI Solver</h3>
      <p>Stuck on a problem? Type it in and get a guided hint for the next step — never the full answer outright.</p>
    </a>
    <a href="formulas.php" class="card">
      <div style="font-size:26px; margin-bottom:10px;">📚</div>
      <h3>Formula Hub</h3>
      <p>Browse the formula library, organized by subject — Algebra, Geometry, Calculus, and more.</p>
    </a>
    <a href="practice.php" class="card">
      <div style="font-size:26px; margin-bottom:10px;">🎯</div>
      <h3>Practice Problems</h3>
      <p>Work through real problems by category, reveal a hint if you're stuck, and check your answer.</p>
    </a>
    <a href="auth/dashboard.php" class="card">
      <div style="font-size:26px; margin-bottom:10px;">📈</div>
      <h3>Progress Tracking</h3>
      <p>See your stats and pick up where you left off, every time you come back.</p>
    </a>
  </div>
</section>

<section class="container">
  <h2>Not Just Answers</h2>
  <div class="compare-grid">
    <div class="compare-card compare-bad">
      <h3>Just the Solution</h3>
      <ul>
        <li>Shows you the final answer, nothing else</li>
        <li>No reasoning behind the steps</li>
        <li>Easy to copy, easy to forget</li>
        <li>You're stuck again on the next similar problem</li>
      </ul>
    </div>
    <div class="compare-card compare-good">
      <h3>AxiomMath</h3>
      <ul>
        <li>One guided hint at a time, never the full answer upfront</li>
        <li>You do the thinking, we point the direction</li>
        <li>Builds understanding that actually sticks</li>
        <li>Every hint ties back to the real formula behind it</li>
      </ul>
    </div>
  </div>
</section>

<section class="container">
  <h2>Try It Now</h2>
  <p style="text-align:center; color:var(--muted); max-width:520px; margin:0 auto;">No sign-up needed — see what a guided hint actually looks like.</p>
  <div class="demo-box">
    <span class="difficulty-badge difficulty-Medium">Live example</span>
    <h3><?php echo htmlspecialchars($demo_problem['question'] ?? 'Solve: 2x² + 5x - 3 = 0'); ?></h3>
    <button type="button" class="reveal-btn"
      data-target="demo-hint" data-show="Show hint" data-hide="Hide hint">Show hint</button>
    <div id="demo-hint" class="reveal-box hidden">
      💡 <?php echo htmlspecialchars($demo_problem['hint'] ?? 'Try factoring by grouping.'); ?>
    </div>
    <p style="margin-top:18px; font-size:13.5px;"><a href="practice.php">Try more problems like this →</a></p>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
  // Reveal/hide toggle for the live demo hint box
  document.querySelectorAll('.reveal-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = document.getElementById(btn.dataset.target);
      target.classList.toggle('hidden');
      btn.textContent = target.classList.contains('hidden') ? btn.dataset.show : btn.dataset.hide;
    });
  });

  // Scroll-in animation for the comparison cards — adds .in-view once
  // each card enters the viewport, which the CSS transitions respond to.
  var compareObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('in-view');
        compareObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.2 });
  document.querySelectorAll('.compare-card').forEach(function (card) {
    compareObserver.observe(card);
  });
</script>

</body>
</html>