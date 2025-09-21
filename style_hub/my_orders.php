<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$orders = $conn->query("SELECT * FROM orders WHERE user_id = " . $_SESSION['user_id'] . " ORDER BY order_date DESC");
?>
<h1>My Order History</h1>
<table border="1">
    <tr><th>Order ID</th><th>Date</th><th>Total</th><th>Status</th><th>Action</th></tr>
    <?php while ($order = $orders->fetch_assoc()): ?>
    <tr>
        <td>#<?php echo $order['id']; ?></td>
        <td><?php echo $order['order_date']; ?></td>
        <td>₹<?php echo $order['total_amount']; ?></td>
        <td><?php echo $order['status']; ?></td>
        <td><a href="order_details.php?id=<?php echo $order['id']; ?>">View Details</a></td>
    </tr>
    <?php endwhile; ?>
</table>
