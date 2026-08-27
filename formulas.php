<?php
session_start();
$base_path = '';
require_once 'auth/db_config.php';

// Pull every formula joined with its category name — this is the
// same JOIN pattern from schema.sql, now actually powering a page
// instead of just sitting in a query you run manually.
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

<?php include 'includes/nav.php'; ?>

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

<?php include 'includes/footer.php'; ?>

</body>
</html>