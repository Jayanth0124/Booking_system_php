<?php
include 'db_connect.php';

// 1. Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Handle the "Place Order" action when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Begin a database transaction
    $conn->begin_transaction();
    
    try {
        // First, handle the shipping address
        $shipping_address_id = $_POST['shipping_address_id'];
        
        // If user entered a new address, insert it first
        if ($shipping_address_id == 'new') {
            $stmt = $conn->prepare("INSERT INTO shipping_addresses (user_id, full_name, address_line1, city, pincode, phone_number) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $user_id, $_POST['full_name'], $_POST['address_line1'], $_POST['city'], $_POST['pincode'], $_POST['phone_number']);
            $stmt->execute();
            $shipping_address_id = $conn->insert_id; // Get the ID of the new address
        }
        
        // Get all items from the user's cart
        $cart_items_result = $conn->query("SELECT p.id as product_id, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");
        
        if ($cart_items_result->num_rows == 0) {
            throw new Exception("Your cart is empty.");
        }
        
        // Calculate total amount on the server-side for security
        $total_amount = 0;
        $cart_items = [];
        while ($item = $cart_items_result->fetch_assoc()) {
            $total_amount += $item['price'] * $item['quantity'];
            $cart_items[] = $item;
        }

        // a. Create a new order in the 'orders' table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, shipping_address_id, total_amount, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("iid", $user_id, $shipping_address_id, $total_amount);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // b. Move cart items to the 'order_items' table
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }

        // c. Clear the user's cart
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        // d. If everything is successful, commit the transaction
        $conn->commit();
        
        // Redirect to a confirmation page
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();

    } catch (Exception $e) {
        // If any step fails, roll back the transaction
        $conn->rollback();
        echo "Order failed: " . $e->getMessage();
    }
}

// 3. Fetch existing addresses and cart total for displaying on the page
$addresses = $conn->query("SELECT * FROM shipping_addresses WHERE user_id = $user_id");
$cart_total_result = $conn->query("SELECT SUM(p.price * c.quantity) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");
$cart_total = $cart_total_result->fetch_assoc()['total'] ?? 0;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
</head>
<body>
    <a href="cart.php">Back to Cart</a>
    <h2>Checkout</h2>

    <div style="border: 1px solid #ddd; padding: 15px;">
        <h3>Order Summary</h3>
        <p><b>Total Amount Payable: ₹<?php echo number_format($cart_total, 2); ?></b></p>
    </div>

    <hr>

    <h3>Select Shipping Address</h3>
    <form method="post" action="checkout.php">
        <?php while ($address = $addresses->fetch_assoc()): ?>
            <p>
                <input type="radio" name="shipping_address_id" value="<?php echo $address['id']; ?>" required>
                <b><?php echo htmlspecialchars($address['full_name']); ?></b><br>
                <?php echo htmlspecialchars($address['address_line1']); ?><br>
                <?php echo htmlspecialchars($address['city']); ?> - <?php echo htmlspecialchars($address['pincode']); ?><br>
                Phone: <?php echo htmlspecialchars($address['phone_number']); ?>
            </p>
        <?php endwhile; ?>
        
        <hr>
        <h4>Or, Add a New Address</h4>
        <p><input type="radio" name="shipping_address_id" value="new" required> Use the new address below:</p>
        
        Full Name: <br>
        <input type="text" name="full_name" size="50"><br><br>
        Address Line 1: <br>
        <input type="text" name="address_line1" size="50"><br><br>
        City: <br>
        <input type="text" name="city"><br><br>
        Pincode: <br>
        <input type="text" name="pincode"><br><br>
        Phone Number: <br>
        <input type="text" name="phone_number"><br><br>

        <button type="submit" style="font-size: 1.2em; padding: 10px;">Place Order</button>
    </form>

</body>
</html>