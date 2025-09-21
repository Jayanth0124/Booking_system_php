<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
// Handle placing the order
if (isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $address = $_POST['shipping_address'];
    $cart_items_result = $conn->query("SELECT p.id as product_id, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");
    
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
        
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt_item->execute();
        }
        
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");
        $conn->commit();
        echo "<h1>Order Placed Successfully!</h1><p>Your order ID is #$order_id</p><a href='my_orders.php'>View Order History</a>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Order failed: " . $e->getMessage();
    }
    exit();
}
?>
<h3>Checkout</h3>
<form method="post">
    Shipping Address: <br>
    <textarea name="shipping_address" rows="4" cols="50" required></textarea><br>
    <button type="submit" name="place_order">Place Order</button>
</form>