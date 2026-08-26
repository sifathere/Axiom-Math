<?php
session_start();
require_once 'auth/db_config.php';

// Practice Problems requires login — this is one of the two features
// that make registering worthwhile, alongside the AI Solver.
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

// Loose-match normalization: real checking, but forgiving about
// spacing/case/formatting differences a student might type differently
// than the stored answer (e.g. "X = -3, 1/2" vs "x = 1/2 or x = -3").
function normalize_answer($str) {
    $str = strtolower($str);
    $str = preg_replace('/\s+/', '', $str);      // remove all whitespace
    $str = str_replace(['²', '^2'], '^2', $str);  // treat ² and ^2 the same
    $str = str_replace('or', ',', $str);          // "or" and "," treated the same
    return $str;
}

$feedback = null; // ['problem_id' => .., 'correct' => bool, 'answer' => ..]

// ---- Handle an answer submission ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['problem_id']) && isset($_POST['submitted_answer'])) {
    $problem_id = (int) $_POST['problem_id'];
    $submitted = trim($_POST['submitted_answer']);

    $stmt = $conn->prepare("SELECT answer FROM practice_problems WHERE problem_id = ?");
    $stmt->bind_param("i", $problem_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $is_correct = (normalize_answer($submitted) === normalize_answer($row['answer'])) ? 1 : 0;

        $insert = $conn->prepare("INSERT INTO user_progress (user_id, problem_id, is_correct) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $_SESSION['user_id'], $problem_id, $is_correct);
        $insert->execute();
        $insert->close();

        $feedback = ['problem_id' => $problem_id, 'correct' => (bool) $is_correct, 'answer' => $row['answer']];
    }
}

// ---- Fetch every practice problem, joined with its category ----
$sql = "SELECT p.problem_id, p.question, p.difficulty, p.hint, p.answer, c.category_name, c.icon
        FROM practice_problems p
        INNER JOIN categories c ON p.category_id = c.category_id
        ORDER BY c.category_name, p.problem_id";
$result = $conn->query($sql);

$problems_by_category = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cat = $row['category_name'];
        if (!isset($problems_by_category[$cat])) {
            $problems_by_category[$cat] = ['icon' => $row['icon'], 'items' => []];
        }
        $problems_by_category[$cat]['items'][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Practice | AxiomMath</title>
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
      <a href="practice.php">Practice</a>
      <a href="solver.php">AI Solver</a>
      <a href="how-it-works.html">How it works</a>
      <a href="about.html">About</a>
    </div>
    <div class="nav-cta">
      <a href="auth/dashboard.php" class="btn-login">Dashboard</a>
      <a href="auth/logout.php" class="btn btn-primary">Log out</a>
    </div>
  </nav>
</header>

<section class="page-intro">
  <span class="symbol" style="top:20%; left:10%; font-size:32px; color:rgba(255,255,255,0.18);">π</span>
  <span class="symbol" style="top:58%; left:86%; font-size:26px; color:rgba(255,255,255,0.18); animation-delay:0.8s;">√</span>
  <span class="eyebrow">Practice</span>
  <h1>Practice Problems</h1>
  <p>Try it yourself first. Reveal a hint if you're stuck, then check your answer.</p>
</section>

<section class="container">
  <?php if ($feedback): ?>
    <div class="save-banner <?php echo $feedback['correct'] ? '' : 'save-banner-wrong'; ?>">
      <?php if ($feedback['correct']): ?>
        ✓ Correct! Saved to your progress.
      <?php else: ?>
        Not quite. The correct answer was:
        <strong><?php echo htmlspecialchars($feedback['answer']); ?></strong>
        — still saved to your progress so you can see it on your dashboard.
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if (empty($problems_by_category)): ?>
    <p style="text-align:center;">No practice problems found. Check that <code>schema.sql</code> has been run and seeded in phpMyAdmin.</p>
  <?php else: ?>
    <?php foreach ($problems_by_category as $category_name => $data): ?>
      <h2 style="margin-top:48px;"><?php echo htmlspecialchars($data['icon']); ?> <?php echo htmlspecialchars($category_name); ?></h2>
      <div class="cards">
        <?php foreach ($data['items'] as $problem): ?>
          <div class="card">
            <span class="difficulty-badge difficulty-<?php echo htmlspecialchars($problem['difficulty']); ?>">
              <?php echo htmlspecialchars($problem['difficulty']); ?>
            </span>
            <h3><?php echo htmlspecialchars($problem['question']); ?></h3>

            <button type="button" class="reveal-btn"
              data-target="hint-<?php echo $problem['problem_id']; ?>"
              data-show="Show hint" data-hide="Hide hint">Show hint</button>

            <div id="hint-<?php echo $problem['problem_id']; ?>" class="reveal-box hidden">
              💡 <?php echo htmlspecialchars($problem['hint']); ?>
            </div>

            <form action="practice.php" method="POST" class="answer-form">
              <input type="hidden" name="problem_id" value="<?php echo $problem['problem_id']; ?>">
              <label for="answer-<?php echo $problem['problem_id']; ?>">Your answer</label>
              <input type="text" id="answer-<?php echo $problem['problem_id']; ?>" name="submitted_answer" required
                placeholder="Type your answer">
              <button type="submit" class="progress-btn correct">Check answer</button>
            </form>
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
          <li><a href="practice.php">Practice</a></li>
          <li><a href="how-it-works.html">How it works</a></li>
          <li><a href="about.html">About</a></li>
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

<script>
  document.querySelectorAll('.reveal-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var target = document.getElementById(btn.dataset.target);
      target.classList.toggle('hidden');
      btn.textContent = target.classList.contains('hidden') ? btn.dataset.show : btn.dataset.hide;
    });
  });
</script>

</body>
</html>