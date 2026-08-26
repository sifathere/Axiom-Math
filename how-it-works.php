<?php
session_start();
$base_path = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>How It Works | AxiomMath</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<section class="page-intro">
  <span class="symbol" style="top:20%; left:10%; font-size:32px; color:rgba(255,255,255,0.18);">√</span>
  <span class="symbol" style="top:58%; left:86%; font-size:26px; color:rgba(255,255,255,0.18); animation-delay:0.8s;">π</span>
  <span class="eyebrow">The process</span>
  <h1>How AxiomMath Works</h1>
  <p>A guided path from confusion to mastery — one step at a time.</p>
</section>

<section class="container">
  <h2>Every Problem Follows the Same Path</h2>
  <p>AxiomMath is built around one idea: you learn math by working through it, not by reading a final answer. Here's exactly what happens each time you open a problem.</p>

  <div class="steps">
    <div class="step-row">
      <div class="tdot">1</div>
      <div class="step-copy">
        <h3>Select a Topic</h3>
        <p>Choose from Algebra, Calculus, Geometry, Trigonometry, Statistics, Linear Algebra, and more.</p>
      </div>
    </div>
    <div class="step-row">
      <div class="tdot">2</div>
      <div class="step-copy">
        <h3>Input Your Problem</h3>
        <p>Type in an equation or formula you're stuck on, or pick one from a practice set matched to your level.</p>
      </div>
    </div>
    <div class="step-row">
      <div class="tdot">3</div>
      <div class="step-copy">
        <h3>Get Guided Hints</h3>
        <p>Learn the logic with adaptive, step-by-step assistance — the next hint, never the final answer outright.</p>
      </div>
    </div>
    <div class="step-row">
      <div class="tdot">4</div>
      <div class="step-copy">
        <h3>Work Through Each Step</h3>
        <p>Try the step yourself before moving on. Still stuck? Ask for another hint instead of skipping ahead.</p>
      </div>
    </div>
    <div class="step-row">
      <div class="tdot">5</div>
      <div class="step-copy">
        <h3>Track Your Progress</h3>
        <p>Every problem you complete updates your progress by topic, so you always know what to revisit before an exam.</p>
      </div>
    </div>
  </div>
</section>

<section class="mission">
  <div class="container">
    <h2>Why Hints Instead of Instant Answers?</h2>
    <p>A final answer only tells you what the result is. A hint teaches you how to get there yourself — and that's the part that's still useful after the test is over.</p>
  </div>
</section>

<section class="container cta-center">
  <a href="index.php" class="btn btn-primary">Start learning →</a>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
