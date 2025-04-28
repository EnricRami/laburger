<?php
require 'dp.php'; // Make sure the filename matches your connection file

header('Content-Type: application/json');

$sql = "SELECT * FROM menu_items";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["success" => false, "error" => "Database query failed."]);
    exit;
}

$menu = [];
while ($row = $result->fetch_assoc()) {
    $menu[] = $row;
}

echo json_encode($menu);

$conn->close();
?>