<?php include 'db_connect.php'; if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); } ?>
<h3>Checkout</h3>
<p>Please enter your shipping address to complete the order.</p>
<form action="place_order.php" method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Address: <textarea name="shipping_address" rows="4" cols="50" required></textarea><br>
    City: <input type="text" name="city" required><br>
    Pincode: <input type="text" name="pincode" required><br>
    <button type="submit" name="place_order">Place Order</button>
</form>