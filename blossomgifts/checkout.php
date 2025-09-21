<?php include 'db_connect.php'; if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); } ?>
<h3>Checkout</h3>
<p>Please enter the delivery details for your order.</p>
<form action="place_order.php" method="post">
    <h4>Recipient & Delivery Details</h4>
    Recipient's Full Name: <input type="text" name="recipient_name" required><br>
    Delivery Address: <textarea name="shipping_address" rows="4" cols="50" required></textarea><br>
    Delivery Date: <input type="date" name="delivery_date" required><br>
    Delivery Time Slot: 
    <select name="delivery_time_slot" required>
        <option value="Morning (9am - 12pm)">Morning (9am - 12pm)</option>
        <option value="Afternoon (12pm - 4pm)">Afternoon (12pm - 4pm)</option>
        <option value="Evening (4pm - 8pm)">Evening (4pm - 8pm)</option>
    </select><br><br>
    <button type="submit" name="place_order">Place Order</button>
</form>