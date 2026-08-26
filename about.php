<?php
session_start();
$base_path = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About | AxiomMath</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<section class="page-intro">
  <span class="symbol" style="top:18%; left:8%; font-size:34px; color:rgba(255,255,255,0.18);">π</span>
  <span class="symbol" style="top:62%; left:88%; font-size:28px; color:rgba(255,255,255,0.18); animation-delay:1s;">∑</span>
  <span class="eyebrow">Our story</span>
  <h1>About AxiomMath</h1>
  <p>Learn. Understand. Solve.</p>
</section>

<section class="container">
  <h2>What is AxiomMath?</h2>
  <p>AxiomMath is an interactive mathematics learning platform designed to help students understand mathematics rather than simply memorize formulas or obtain answers.</p>
  <p>By combining equation solving, guided hints, formula references, practice exercises, and progress tracking into one platform, AxiomMath makes learning mathematics easier, smarter, and more enjoyable.</p>
</section>

<section class="mission">
  <div class="container">
    <h2>Our Mission</h2>
    <p>Our mission is to make mathematics simple, interactive, and accessible for every learner. We believe that students learn better when they understand concepts instead of memorizing formulas.</p>
    <p>AxiomMath encourages curiosity, confidence, and independent problem-solving through an engaging learning experience.</p>
  </div>
</section>

<section class="container">
  <h2>What We Offer</h2>
  <div class="cards">
    <div class="card">
      <h3>📚 Formula Library</h3>
      <p>Explore a well-organized collection of mathematical formulas categorized by topic.</p>
    </div>
    <div class="card">
      <h3>🧮 Equation Solver</h3>
      <p>Solve mathematical equations quickly and accurately with easy-to-understand solutions.</p>
    </div>
    <div class="card">
      <h3>💡 Guided Hints</h3>
      <p>Receive helpful hints that teach the solving process instead of only displaying answers.</p>
    </div>
    <div class="card">
      <h3>🎯 Practice Problems</h3>
      <p>Strengthen your mathematical skills with interactive practice exercises.</p>
    </div>
    <div class="card">
      <h3>📈 Progress Tracking</h3>
      <p>Track your learning progress and monitor your achievements over time.</p>
    </div>
    <div class="card">
      <h3>🌐 User-Friendly Interface</h3>
      <p>Enjoy a clean, modern, and responsive interface designed for all devices.</p>
    </div>
  </div>
</section>

<section class="choose">
  <div class="container">
    <h2>Why Choose AxiomMath?</h2>
    <ul>
      <li>✔ Learn concepts instead of memorizing formulas.</li>
      <li>✔ Interactive and engaging learning environment.</li>
      <li>✔ Simple, clean, and responsive design.</li>
      <li>✔ Suitable for students of all skill levels.</li>
      <li>✔ Accurate mathematical tools and resources.</li>
      <li>✔ Supports continuous learning and self-improvement.</li>
    </ul>
  </div>
</section>

<section class="container">
  <h2>Our Future Vision</h2>
  <div class="future">
    <div>🤖 AI Math Tutor</div>
    <div>📷 OCR Equation Scanner</div>
    <div>📝 Step-by-Step Solutions</div>
    <div>🎮 Gamified Learning</div>
    <div>📱 Mobile Application</div>
    <div>👨‍🏫 Teacher Dashboard</div>
    <div>🎙 Voice Assistance</div>
    <div>🎯 Personalized Learning</div>
  </div>
</section>

<section class="goal">
  <div class="container">
    <h2>Our Goal</h2>
    <blockquote>"To empower every student with confidence in mathematics by transforming difficult problems into meaningful learning experiences."</blockquote>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>
