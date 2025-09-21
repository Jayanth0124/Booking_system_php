<?php
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all orders for the current user, ordered by the most recent first
$orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$orders->bind_param("i", $user_id);
$orders->execute();
$orders_result = $orders->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Order History</title>
</head>
<body>
    <a href="my_account.php">Back to My Account</a>
    <h1>My Order History</h1>

    <?php if ($orders_result->num_rows > 0): ?>
        <?php while ($order = $orders_result->fetch_assoc()): ?>
            <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                <h3>Order #<?php echo htmlspecialchars($order['id']); ?></h3>
                <p><b>Status:</b> <?php echo htmlspecialchars($order['status']); ?></p>
                <p><b>Order Total:</b> ₹<?php echo htmlspecialchars($order['total_amount']); ?></p>
                <p><b>Delivery Date:</b> <?php echo htmlspecialchars($order['delivery_date'] . ' at ' . $order['delivery_time_slot']); ?></p>
                <p><b>Recipient:</b> <?php echo htmlspecialchars($order['recipient_name']); ?></p>
                <p><b>Shipping Address:</b> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                
                <h4>Items in this Order:</h4>
                <table border="1" style="width:100%; border-collapse: collapse;">
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price Per Item</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php 
                    // Fetch items for this specific order
                    $items_stmt = $conn->prepare("SELECT oi.quantity, oi.price_per_item, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                    $items_stmt->bind_param("i", $order['id']);
                    $items_stmt->execute();
                    $items_result = $items_stmt->get_result();
                    while ($item = $items_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>₹<?php echo htmlspecialchars($item['price_per_item']); ?></td>
                        <td>₹<?php echo htmlspecialchars($item['quantity'] * $item['price_per_item']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You have not placed any orders yet.</p>
    <?php endif; ?>

    <hr>
    <a href="index.php">Continue Shopping</a>
</body>
</html>