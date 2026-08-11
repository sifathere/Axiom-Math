<?php
require_once 'auth/db_config.php';


$sql = "SELECT f.title, f.expression, f.description, c.category_name, c.icon
        FROM formulas f
        INNER JOIN categories c ON f.category_id = c.category_id
        ORDER BY c.category_name, f.title";
$result = $conn->query($sql);

$formulas_by_category = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cat = $row['category_name'];
        if (!isset($formulas_by_category[$cat])) {
            $formulas_by_category[$cat] = ['icon' => $row['icon'], 'items' => []];
        }
        $formulas_by_category[$cat]['items'][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Formula Library | AxiomMath</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
  <nav>
    <a href="index.html" class="logo"><span class="mark">∫</span>AxiomMath</a>
    <div class="nav-links">
      <a href="formulas.php">Formula Hub</a>
      <a href="solver.php">AI Solver</a>
      <a href="how-it-works.html">How it works</a>
      <a href="about.html">About</a>
    </div>
    <div class="nav-cta">
      <a href="./auth/login.php" class="btn-login">Log in</a>
      <a href="./auth/register.php" class="btn btn-primary">Sign up free</a>
    </div>
  </nav>
</header>

<section class="page-intro">
  <span class="symbol" style="top:18%; left:8%; font-size:34px; color:rgba(255,255,255,0.18);">Σ</span>
  <span class="symbol" style="top:62%; left:88%; font-size:28px; color:rgba(255,255,255,0.18); animation-delay:1s;">√</span>
  <span class="eyebrow">Formula Hub</span>
  <h1>Formula Library</h1>
  <p>Every formula in the database, organized by subject.</p>
</section>

<section class="container">
  <?php if (empty($formulas_by_category)): ?>
    <p style="text-align:center;">No formulas found. Check that <code>schema.sql</code> has been run and seeded in phpMyAdmin.</p>
  <?php else: ?>
    <?php foreach ($formulas_by_category as $category_name => $data): ?>
      <h2 style="margin-top:48px;"><?php echo htmlspecialchars($data['icon']); ?> <?php echo htmlspecialchars($category_name); ?></h2>
      <div class="cards">
        <?php foreach ($data['items'] as $formula): ?>
          <div class="card">
            <h3><?php echo htmlspecialchars($formula['title']); ?></h3>
            <div class="formula-expr"><?php echo htmlspecialchars($formula['expression']); ?></div>
            <p><?php echo htmlspecialchars($formula['description']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <div class="footer-logo"><span class="mark" style="width:26px;height:26px;font-size:13px;">∫</span>AxiomMath</div>
        <p style="font-size:14px; line-height:1.6;">Making mathematics understandable, one guided step at a time.</p>
      </div>
      <div>
        <h4>Quick links</h4>
        <ul>
          <li><a href="formulas.php">Formula library</a></li>
          <li><a href="how-it-works.html">How it works</a></li>
          <li><a href="about.html">About</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
      </div>
      <div>
        <h4>Resources</h4>
        <ul>
          <li><a href="#">Privacy</a></li>
          <li><a href="#">Terms</a></li>
          <li><a href="#">Help</a></li>
        </ul>
      </div>
      <div>
        <h4>Follow</h4>
        <ul>
          <li><a href="#">GitHub</a></li>
          <li><a href="#">LinkedIn</a></li>
          <li><a href="#">YouTube</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2026 AxiomMath. All rights reserved.</span>
      <div class="socials">
        <a href="#">GitHub</a>
        <a href="#">LinkedIn</a>
        <a href="#">YouTube</a>
      </div>
    </div>
  </div>
</footer>

</body>
</html>
