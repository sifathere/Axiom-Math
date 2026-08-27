<?php
session_start();
$base_path = '';

$is_logged_in = isset($_SESSION['user_id']);

require_once 'ai_config.php';

$error_message = "";
$ai_response = "";
$question = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question'] ?? '');

    if (empty($question)) {
        $error_message = "Please type a math problem first.";
    } elseif (!$is_logged_in) {
        // Guests can see and use the form, but the actual AI call —
        // the feature that costs real API usage — requires an account.
        $error_message = 'login_required';
    } elseif (GEMINI_API_KEY === 'PASTE_YOUR_KEY_HERE' || empty(GEMINI_API_KEY)) {
        $error_message = "No API key set yet. Add your free Gemini key to ai_config.php.";
    } else {
        // The core AxiomMath rule, enforced in the prompt itself: a hint for the
        // next step, never the full worked answer.
        $prompt = "You are a patient, encouraging math tutor for a platform called AxiomMath. "
                . "A student is working on this problem: \"" . $question . "\". "
                . "Give exactly ONE short guided hint for the very next step only — never the full "
                . "final answer or a complete worked solution. Keep it to 2-3 sentences. End by "
                . "inviting them to ask again if they want the next hint after trying this step. "
                . "IMPORTANT: Do not use LaTeX formatting or dollar-sign math delimiters (like \$2x\$ "
                . "or \\(x^2\\)). Write all math in plain text instead — for example, write 2x, x^2, "
                . "or sqrt(x) with no special symbols wrapping them.";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/"
             . GEMINI_MODEL . ":generateContent?key=" . GEMINI_API_KEY;

        $payload = json_encode([
            "contents" => [[
                "parts" => [["text" => $prompt]]
            ]]
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            $error_message = "Could not reach the AI service: " . $curl_error;
        } elseif ($http_code !== 200) {
            switch ($http_code) {
                case 400:
                    $error_message = "The AI service couldn't process that request. Try rephrasing the problem.";
                    break;
                case 401:
                case 403:
                    $error_message = "Your API key looks invalid or unauthorized. Double-check it was copied "
                                    . "correctly (in full, no extra spaces) into ai_config.php.";
                    break;
                case 404:
                    $error_message = "The model name in ai_config.php may be outdated. Check "
                                    . "aistudio.google.com for the current free model name and update GEMINI_MODEL.";
                    break;
                case 429:
                    $error_message = "You've hit the free tier's rate limit. Wait about a minute and try again.";
                    break;
                case 503:
                    $error_message = "Google's AI service is temporarily overloaded (not a problem with your "
                                    . "setup). Wait a few seconds and try again.";
                    break;
                default:
                    $error_message = "AI service returned an unexpected error (HTTP $http_code). Try again shortly.";
            }
        } else {
            $data = json_decode($response, true);
            $ai_response = $data['candidates'][0]['content']['parts'][0]['text'] ?? "";
            // Safety net: strip any LaTeX $ delimiters that slip through despite the
            // prompt instruction — the AI mostly follows it, but not 100% of the time.
            $ai_response = str_replace('$', '', $ai_response);
            if (empty($ai_response)) {
                $error_message = "The AI didn't return a hint. Try rephrasing the problem.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Solver | AxiomMath</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/nav.php'; ?>

<section class="page-intro">
  <span class="symbol" style="top:20%; left:10%; font-size:32px; color:rgba(255,255,255,0.18);">Σ</span>
  <span class="symbol" style="top:58%; left:86%; font-size:26px; color:rgba(255,255,255,0.18); animation-delay:0.8s;">∞</span>
  <span class="eyebrow">AI Solver</span>
  <h1>Get a Guided Hint</h1>
  <p>Type a problem. Get a hint for the next step — not the whole answer.</p>
</section>

<section class="container">
  <div class="solver-box">
    <?php if ($error_message === 'login_required'): ?>
      <div class="auth-error">Please <a href="auth/login.php">log in</a> to use the AI Solver.</div>
    <?php elseif (!empty($error_message)): ?>
      <div class="auth-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form action="solver.php" method="POST">
      <label for="question" style="display:block; font-size:13px; font-weight:600; color:var(--dark); margin-bottom:6px;">Your problem</label>
      <textarea id="question" name="question" required rows="3"
        placeholder="e.g., Solve 2x^2 + 5x - 3 = 0"><?php echo htmlspecialchars($question); ?></textarea>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Get a hint</button>
    </form>

    <?php if (!empty($ai_response)): ?>
      <div class="hint-box">
        <h3>Hint</h3>
        <p><?php echo nl2br(htmlspecialchars($ai_response)); ?></p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

</body>
</html>