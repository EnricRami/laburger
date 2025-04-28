<?php
session_start(); // add this
require 'dp.php';
header('Content-Type: application/json');
ini_set('display_errors', 1); error_reporting(E_ALL);

if (empty($_POST['email']) || empty($_POST['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Email and password are required."
    ]);
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['password'];

// ---- USER CHECK ----
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$r1 = $stmt->get_result();

if ($r1 && $r1->num_rows === 1) {
    $u = $r1->fetch_assoc();
    if ($password === $u['password']) {
        echo json_encode([
            "success" => true,
            "user_id" => $u['id'],
            "role" => "user"
        ]);
        $stmt->close();
        $conn->close();
        exit;
    }
}
$stmt->close();

// ---- ADMIN CHECK ----
$stmt2 = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
$stmt2->bind_param("s", $email);
$stmt2->execute();
$r2 = $stmt2->get_result();

if ($r2 && $r2->num_rows === 1) {
    $a = $r2->fetch_assoc();
    if ($password === $a['password']) {
        $_SESSION['admin_id'] = $a['id']; // <--- set session for admin
        echo json_encode([
            "success" => true,
            "admin_id" => $a['id'],
            "role" => "admin"
        ]);
        $stmt2->close();
        $conn->close();
        exit;
    }
}   