<?php
include 'db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if an order ID was passed in the URL
if (!isset($_GET['order_id'])) {
    echo "No order specified.";
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Fetch the order details to confirm it belongs to the current user
$stmt = $conn->prepare("SELECT id, total_amount, shipping_address FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "Could not find your order confirmation.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Successful</title>
    <style>
        .container { border: 2px solid green; padding: 20px; text-align: center; max-width: 600px; margin: 40px auto; font-family: sans-serif; }
        .details { text-align: left; border-top: 1px solid #ccc; padding-top: 15px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Order Placed Successfully!</h1>
        <p>Thank you for shopping with StyleHub. A confirmation has been sent to your email.</p>
        
        <div class="details">
            <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($order['id']); ?></p>
            <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Shipping to:</strong><br><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
        </div>

        <hr>
        <a href="my_orders.php">View Order History</a> | 
        <a href="index.php">Continue Shopping</a>
    </div>
</body>
</html>