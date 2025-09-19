<?php
include 'db_connect.php';

// 1. Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if an order ID was passed in the URL
if (!isset($_GET['order_id'])) {
    echo "No order specified.";
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// 3. Fetch the order details to confirm it belongs to the current user and display info
$stmt = $conn->prepare("
    SELECT o.id, o.total_amount, sa.full_name, sa.address_line1, sa.city, sa.pincode
    FROM orders o
    JOIN shipping_addresses sa ON o.shipping_address_id = sa.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// If the order doesn't exist or doesn't belong to the user, show an error
if (!$order) {
    echo "Could not find your order confirmation.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <style>
        .container { border: 2px solid green; padding: 20px; text-align: center; max-width: 600px; margin: 40px auto; }
        .shipping-details { text-align: left; border: 1px solid #ccc; padding: 15px; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="container">
        <h1>✅ Thank You! Your Order is Confirmed.</h1>
        <p>We have received your order and are getting it ready for you.</p>
        
        <p>Your Order ID is: <strong><?php echo htmlspecialchars($order['id']); ?></strong></p>
        <p>Total Amount Paid: <strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></p>

        <div class="shipping-details">
            <h4>Shipping to:</h4>
            <p>
                <strong><?php echo htmlspecialchars($order['full_name']); ?></strong><br>
                <?php echo htmlspecialchars($order['address_line1']); ?><br>
                <?php echo htmlspecialchars($order['city']); ?> - <?php echo htmlspecialchars($order['pincode']); ?>
            </p>
        </div>

        <hr>
        <a href="my_orders.php">View My Orders</a> | 
        <a href="index.php">Continue Shopping</a>
    </div>

</body>
</html>