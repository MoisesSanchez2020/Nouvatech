<?php
// ---------------------------------------------
// NouvaTech - Home Contact Form Email Handler
// ---------------------------------------------

// 1. Email destino
$to = "info@nouvatech.com";
$subject = "New Tech Evaluation Request";

// 2. Capturar datos del form
$name    = htmlspecialchars($_POST['name'] ?? '');
$email   = htmlspecialchars($_POST['email'] ?? '');
$phone   = htmlspecialchars($_POST['phone'] ?? '');
$message = htmlspecialchars($_POST['message'] ?? '');

// Validación rápida
if (!$name || !$email || !$message) {
    echo "error";
    exit;
}

// 3. Construir el mensaje en HTML
$body = "
  <h2>New Tech Evaluation Form Submission</h2>
  <p><strong>Name:</strong> {$name}</p>
  <p><strong>Email:</strong> {$email}</p>
  <p><strong>Phone:</strong> {$phone}</p>
  <p><strong>Message:</strong><br>{$message}</p>
  <hr>
  <p>Sent from NouvaTech website contact form</p>
";

// 4. Headers correctos y compatibles con SPF
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: NouvaTech Website <info@nouvatech.com>\r\n";
$headers .= "Reply-To: {$email}\r\n";

// 5. Enviar correo
if (mail($to, $subject, $body, $headers)) {
    echo "success";
} else {
    echo "error";
}
?>
