<?php
include 'db_connect.php';
if (!isset($_POST['place_order']) || !isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$user_id = $_SESSION['user_id'];
$cart_items_result = $conn->query("SELECT p.id as product_id, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");

$conn->begin_transaction();
try {
    $total_amount = 0;
    $cart_items = [];
    while ($item = $cart_items_result->fetch_assoc()) {
        $total_amount += $item['price'] * $item['quantity'];
        $cart_items[] = $item;
    }
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, recipient_name, shipping_address, delivery_date, delivery_time_slot) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssss", $user_id, $total_amount, $_POST['recipient_name'], $_POST['shipping_address'], $_POST['delivery_date'], $_POST['delivery_time_slot']);
    $stmt->execute();
    $order_id = $conn->insert_id;
    
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt_item->execute();
    }
    
    $conn->query("DELETE FROM cart WHERE user_id = $user_id");
    $conn->commit();
    header("Location: order_success.php?order_id=" . $order_id);
} catch (Exception $e) {
    $conn->rollback();
    echo "Order failed: " . $e->getMessage();
}
?>