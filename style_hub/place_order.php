<?php
include 'db_connect.php';
if (!isset($_POST['place_order']) || !isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$user_id = $_SESSION['user_id'];
$address = $_POST['full_name'] . "\n" . $_POST['shipping_address'] . "\n" . $_POST['city'] . " - " . $_POST['pincode'];
$cart_items_result = $conn->query("SELECT p.id as product_id, p.price, c.quantity, c.size FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");

$conn->begin_transaction();
try {
    $total_amount = 0;
    $cart_items = [];
    while ($item = $cart_items_result->fetch_assoc()) {
        $total_amount += $item['price'] * $item['quantity'];
        $cart_items[] = $item;
    }
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $user_id, $total_amount, $address);
    $stmt->execute();
    $order_id = $conn->insert_id;
    
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, size, price_per_item) VALUES (?, ?, ?, ?, ?)");
    $stmt_stock = $conn->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
    
    foreach ($cart_items as $item) {
        $stmt_item->bind_param("iiisd", $order_id, $item['product_id'], $item['quantity'], $item['size'], $item['price']);
        $stmt_item->execute();
        $stmt_stock->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt_stock->execute();
    }
    
    $conn->query("DELETE FROM cart WHERE user_id = $user_id");
    $conn->commit();
    header("Location: order_success.php?order_id=" . $order_id);
} catch (Exception $e) {
    $conn->rollback();
    echo "Order failed: " . $e->getMessage();
}
?>