<?php
// Contact form handler — OWASP A03/A07 hardening
header('Content-Type: application/json');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');

// A07: rate limiting (10 submissions per hour per IP, stored in /tmp)
$ip   = md5($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$file = sys_get_temp_dir() . "/cf_rl_{$ip}";
$now  = time();
$hits = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
$hits = array_filter($hits, fn($t) => $t > $now - 3600);
if (count($hits) >= 10) {
    http_response_code(429);
    echo json_encode(['status'=>'error','message'=>'Too many requests']);
    exit;
}
$hits[] = $now;
file_put_contents($file, json_encode(array_values($hits)));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>'error','message'=>'Method not allowed']);
    exit;
}

// A03: honeypot check
if (!empty($_POST['bot-field'])) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'Bad request']);
    exit;
}

// A03: sanitise + validate
$name    = trim(htmlspecialchars(strip_tags($_POST['name']    ?? ''), ENT_QUOTES, 'UTF-8'));
$email   = filter_var(trim($_POST['email']   ?? ''), FILTER_VALIDATE_EMAIL);
$phone   = trim(htmlspecialchars(strip_tags($_POST['phone']   ?? ''), ENT_QUOTES, 'UTF-8'));
$service = trim(htmlspecialchars(strip_tags($_POST['service'] ?? ''), ENT_QUOTES, 'UTF-8'));
$message = trim(htmlspecialchars(strip_tags($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8'));

if (!$name || strlen($name) < 2 || !$email || strlen($message) < 10) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'Missing required fields']);
    exit;
}

$subject = "New Contact Form Submission — TRUEWEATHER HVAC";
$body    = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nService: {$service}\n\nMessage:\n{$message}";
$headers = "From: noreply@example.com\r\n"
         . "Reply-To: {$email}\r\n"
         . "X-Mailer: PHP/" . phpversion();

$sent = mail('trueweatherhvac@gmail.com', $subject, $body, $headers);
if ($sent) {
    header('Location: thank-you.html');
    exit;
} else {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>'Mail server unavailable']);
}
?>