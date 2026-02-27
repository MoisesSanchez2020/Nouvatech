<?php
header('Content-Type: application/json');

// Only allow POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

/* ================= RECAPTCHA VALIDATION ================= */

// 🔒 Replace with your actual secret key
$secretKey = "6Ld2LmQsAAAAAC0Eph2AdAEvigZr_C0ZAATk1kOg";

$captcha = $_POST['g-recaptcha-response'] ?? '';

if (empty($captcha)) {
    echo json_encode(["success" => false, "error" => "Captcha missing"]);
    exit;
}

// Verify with Google
$verifyUrl = "https://www.google.com/recaptcha/api/siteverify";
$verifyResponse = file_get_contents($verifyUrl . "?secret=" . urlencode($secretKey) . "&response=" . urlencode($captcha));

if ($verifyResponse === false) {
    echo json_encode(["success" => false, "error" => "Captcha verification failed"]);
    exit;
}

$responseData = json_decode($verifyResponse);

if (!$responseData || !$responseData->success) {
    echo json_encode(["success" => false, "error" => "Captcha failed"]);
    exit;
}

/* ================= FORM VALIDATION ================= */

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');
$solutions = $_POST['solutions'] ?? [];

if (empty($name) || empty($email) || empty($phone) || empty($message)) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "error" => "Invalid email address"]);
    exit;
}

// Prevent email header injection
$email = str_replace(["\r", "\n"], '', $email);

// Sanitize checkbox values
$solutions = is_array($solutions) ? array_map('strip_tags', $solutions) : [];
$solutionsText = !empty($solutions) ? implode(', ', $solutions) : 'None selected';

/* ================= EMAIL PREPARATION ================= */

$to = "info@nouvatech.com";
$subject = "New IT Assessment Request";

$body = "New IT Assessment Request\n\n";
$body .= "Name: {$name}\n";
$body .= "Email: {$email}\n";
$body .= "Phone: {$phone}\n";
$body .= "Solutions: {$solutionsText}\n\n";
$body .= "Message:\n{$message}\n";

$headers = "From: noreply@nouvatech.com\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

/* ================= SEND EMAIL ================= */

if (!mail($to, $subject, $body, $headers)) {
    echo json_encode(["success" => false, "error" => "Mail delivery failed"]);
    exit;
}

/* ================= SUCCESS ================= */

echo json_encode(["success" => true]);
exit;
