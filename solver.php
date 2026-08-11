<?php
require_once 'ai_config.php';

$error_message = "";
$ai_response = "";
$question = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question'] ?? '');

    if (empty($question)) {
        $error_message = "Please type a math problem first.";
    } elseif (GEMINI_API_KEY === 'PASTE_YOUR_KEY_HERE' || empty(GEMINI_API_KEY)) {
        $error_message = "No API key set yet. Add your free Gemini key to ai_config.php.";
    } else {
        // The core AxiomMath rule, enforced in the prompt itself: a hint for the
        // next step, never the full worked answer.
        $prompt = "You are a patient, encouraging math tutor for a platform called AxiomMath. "
                . "A student is working on this problem: \"" . $question . "\". "
                . "Give exactly ONE short guided hint for the very next step only — never the full "
                . "final answer or a complete worked solution. Keep it to 2-3 sentences. End by "
                . "inviting them to ask again if they want the next hint after trying this step.";

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
            $error_message = "AI service returned an error (HTTP $http_code). "
                            . "Double-check the API key in ai_config.php.";
        } else {
            $data = json_decode($response, true);
            $ai_response = $data['candidates'][0]['content']['parts'][0]['text'] ?? "";
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
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          primary: '#2563EB',
          'primary-dark': '#1d4ed8',
          accent: '#38BDF8',
          dark: '#0F172A',
          muted: '#64748B',
          line: '#E2E8F0',
        },
        fontFamily: {
          display: ['"Space Grotesk"', 'sans-serif'],
          sans: ['Inter', 'sans-serif'],
          mono: ['"JetBrains Mono"', 'monospace'],
        },
      },
    },
  };
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
</head>
<body class="bg-[#F8FAFC] font-sans text-dark antialiased min-h-screen">

<div class="max-w-4xl mx-auto px-6 pt-6">
  <a href="index.html" class="inline-flex items-center gap-2 font-display font-bold text-lg text-dark hover:text-primary">
    <span class="w-7 h-7 rounded-lg bg-primary text-white flex items-center justify-center font-mono text-sm">∫</span>
    AxiomMath
  </a>
</div>

<div class="max-w-2xl mx-auto p-6">
  <header class="mb-8 text-center">
    <h1 class="font-display text-3xl font-bold text-dark">AI Solver</h1>
    <p class="text-muted mt-2">Type a problem. Get a hint for the next step — not the whole answer.</p>
  </header>

  <main class="bg-white p-8 rounded-2xl shadow-md border border-line">
    <?php if (!empty($error_message)): ?>
      <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 text-sm">
        <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>

    <form action="solver.php" method="POST" class="space-y-4">
      <div>
        <label for="question" class="block text-sm font-semibold text-dark mb-1">Your problem</label>
        <textarea id="question" name="question" required rows="3"
          class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-primary focus:outline-none font-mono text-sm"
          placeholder="e.g., Solve 2x^2 + 5x - 3 = 0"><?php echo htmlspecialchars($question); ?></textarea>
      </div>

      <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2.5 rounded-lg transition">
        Get a hint
      </button>
    </form>

    <?php if (!empty($ai_response)): ?>
      <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-5">
        <h3 class="text-xs font-semibold text-primary uppercase tracking-wider mb-2">Hint</h3>
        <p class="text-dark leading-relaxed"><?php echo nl2br(htmlspecialchars($ai_response)); ?></p>
      </div>
    <?php endif; ?>
  </main>
</div>

</body>
</html>
