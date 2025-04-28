<?php
$host = 'localhost';
$user = 'root';
$pass = '123456780';
$dbname = 'laburger';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset('utf8mb4');
?>