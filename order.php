<?php
// order.php
ini_set('display_errors',1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Use exceptions for mysqli errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require 'dp.php';  // your DB connection

try {
    // 1) Decode & validate payload
    $input = json_decode(file_get_contents('php://input'), true);
    if (
        !isset($input['user_id'], $input['cart']) ||
        !is_array($input['cart']) ||
        count($input['cart']) === 0
    ) {
        throw new Exception('Invalid order data');
    }
    $user_id = intval($input['user_id']);
    $cart    = $input['cart'];

    // 2) Compute total
    $total = 0.0;
    foreach ($cart as $item) {
        $price    = floatval($item['price']);
        $quantity = intval($item['quantity']);
        $total   += $price * $quantity;
    }

    // 3) Insert into orders(user_id, total)
    $conn->begin_transaction();
    $stmt = $conn->prepare(
      "INSERT INTO orders (user_id, total) VALUES (?, ?)"
    );
    $stmt->bind_param('id', $user_id, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // 4) Insert each item into order_items(order_id, menu_item_id, quantity)
    $stmt = $conn->prepare(
      "INSERT INTO order_items (order_id, menu_item_id, quantity)
       VALUES (?, ?, ?)"
    );
    foreach ($cart as $item) {
        $menu_item_id = intval($item['id']);
        $quantity     = intval($item['quantity']);
        $stmt->bind_param('iii', $order_id, $menu_item_id, $quantity);
        $stmt->execute();
    }
    $stmt->close();

    // 5) Commit & return success
    $conn->commit();
    echo json_encode([
      'success'  => true,
      'order_id' => $order_id
    ]);
    exit;
}
catch (mysqli_sql_exception $sqlEx) {
    // DB error
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
      'success' => false,
      'error'   => 'Database error: '.$sqlEx->getMessage()
    ]);
    exit;
}
catch (Exception $ex) {
    // Validation or other error
    if ($conn->in_transaction) {
      $conn->rollback();
    }
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'error'   => 'Order error: '.$ex->getMessage()
    ]);
    exit;
}