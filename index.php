<?php
session_start();
$base_path = '';
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

<?php include 'includes/footer.php'; ?>

</body>
</html>
