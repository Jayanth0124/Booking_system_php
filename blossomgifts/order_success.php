<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) { header("Location: index.php"); exit(); }
$order = $conn->query("SELECT * FROM orders WHERE id = ".(int)$_GET['order_id']." AND user_id = ".$_SESSION['user_id'])->fetch_assoc();
?>
<div style="border: 2px solid green; padding: 20px; text-align: center;">
    <h1>✅ Order Placed Successfully!</h1>
    <p>Your Order ID is: <strong>#<?php echo htmlspecialchars($order['id']); ?></strong></p>
    <p>It will be delivered to <strong><?php echo htmlspecialchars($order['recipient_name']); ?></strong> on <strong><?php echo htmlspecialchars($order['delivery_date']); ?></strong>.</p>
    <a href="my_orders.php">View Order History</a> | <a href="index.php">Continue Shopping</a>
</div>