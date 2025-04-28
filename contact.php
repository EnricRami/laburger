<?php
// contact.php
ini_set('display_errors',1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require 'dp.php';  // include your DB connection

// 1) Collect & validate input
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    echo json_encode([
      'success' => false,
      'error'   => 'All fields are required.'
    ]);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
      'success' => false,
      'error'   => 'Invalid email address.'
    ]);
    exit;
}

try {
    // 2) Insert into contact_us table
    $stmt = $conn->prepare(
      "INSERT INTO contact_us (name, email, message)
       VALUES (?, ?, ?)"
    );
    $stmt->bind_param('sss', $name, $email, $message);
    $stmt->execute();
    $stmt->close();

    // 3) (Optional) send email notification
    $to      = 'contact@laburger.com';
    $subject = "New message from $name";
    $body    = "Name:    $name\n";
    $body   .= "Email:   $email\n\n";
    $body   .= "Message:\n$message\n";
    $headers = "From: $name <$email>\r\n"
             . "Reply-To: $email\r\n";

    // You can skip mail() on local dev if it’s not configured
    @mail($to, $subject, $body, $headers);

    // 4) Return success
    echo json_encode(['success' => true]);
}
catch (Exception $e) {
    echo json_encode([
      'success' => false,
      'error'   => 'Server error, please try again.'
    ]);
}
$conn->close();