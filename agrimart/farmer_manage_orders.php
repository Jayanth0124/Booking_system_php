<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') { header("Location: login.php"); exit(); }
$farmer = $conn->query("SELECT id FROM farmers WHERE user_id = ".$_SESSION['user_id'])->fetch_assoc();
$farmer_id = $farmer['id'];

// Handle status updates
if(isset($_POST['update_status'])) {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['status'], $_POST['order_id']);
    $stmt->execute();
}

// Fetch orders containing products sold by this farmer
$orders = $conn->query("SELECT DISTINCT o.*, u.username FROM orders o JOIN order_items oi ON o.id = oi.order_id JOIN products p ON oi.product_id = p.id JOIN users u ON o.user_id = u.id WHERE p.farmer_id = $farmer_id");
?>
<h3>My Customer Orders</h3>
<table border="1">
    <tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Status</th><th>Update</th></tr>
    <?php while ($order = $orders->fetch_assoc()): ?>
    <tr>
        <td>#<?php echo $order['id']; ?></td>
        <td><?php echo htmlspecialchars($order['username']); ?></td>
        <td><?php echo $order['order_date']; ?></td>
        <td>₹<?php echo $order['total_amount']; ?></td>
        <td><b><?php echo $order['status']; ?></b></td>
        <td>
            <form method="post" style="margin:0;">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <select name="status">
                    <option value="Processing" <?php if($order['status']=='Processing') echo 'selected'; ?>>Processing</option>
                    <option value="Shipped" <?php if($order['status']=='Shipped') echo 'selected'; ?>>Shipped</option>
                </select>
                <button type="submit" name="update_status">Update</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>