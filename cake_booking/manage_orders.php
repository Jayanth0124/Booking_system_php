<?php
include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }
// Handle Status Update
if (isset($_POST['update_status'])) {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['status'], $_POST['order_id']);
    $stmt->execute();
}
$orders = $conn->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC");
?>
<h3>Manage Customer Orders</h3>
<table border="1">
    <tr><th>Order ID</th><th>User</th><th>Date</th><th>Total</th><th>Status</th><th>Update Status</th></tr>
    <?php while ($order = $orders->fetch_assoc()): ?>
    <tr>
        <td><?php echo $order['id']; ?></td>
        <td><?php echo htmlspecialchars($order['username']); ?></td>
        <td><?php echo $order['order_date']; ?></td>
        <td>₹<?php echo $order['total_amount']; ?></td>
        <td><b><?php echo $order['status']; ?></b></td>
        <td>
            <form method="post" style="margin:0;">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <select name="status">
                    <option value="Pending" <?php if($order['status']=='Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Processing" <?php if($order['status']=='Processing') echo 'selected'; ?>>Processing</option>
                    <option value="Shipped" <?php if($order['status']=='Shipped') echo 'selected'; ?>>Shipped</option>
                    <option value="Delivered" <?php if($order['status']=='Delivered') echo 'selected'; ?>>Delivered</option>
                    <option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                </select>
                <button type="submit" name="update_status">Update</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>