<?php
require 'dp.php';
header('Content-Type: application/json');

if (
    empty($_POST['username']) ||
    empty($_POST['email']) ||
    empty($_POST['password']) ||
    empty($_POST['phone']) ||
    empty($_POST['address'])
) {
    echo json_encode(["success" => false, "error" => "All fields are required."]);
    exit;
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$phone = trim($_POST['phone']);
$address = trim($_POST['address']);

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(["success" => false, "error" => "Email already registered. Please use a different email or Login directly."]);
    exit;
}
$stmt->close();

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, phone, address) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $password, $phone, $address);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Registration failed."]); //Isn't saved in the database
}
$stmt->close();
$conn->close();
?>