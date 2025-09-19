<?php /* Code to view and process refunds */
include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }
if(isset($_GET['process_id'])) {
    $stmt = $conn->prepare("UPDATE refunds SET status = 'Processed' WHERE id = ?");
    $stmt->bind_param("i", $_GET['process_id']);
    $stmt->execute();
}
$refunds = $conn->query("SELECT r.*, b.pnr_number FROM refunds r JOIN bookings b ON r.booking_id = b.id WHERE r.status = 'Pending'");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Pending Refunds</h3>
<table border="1">
    <tr><th>PNR</th><th>Refund Amount</th><th>Requested At</th><th>Action</th></tr>
    <?php while($row = $refunds->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['pnr_number']); ?></td>
        <td>₹<?php echo $row['refund_amount']; ?></td>
        <td><?php echo $row['requested_at']; ?></td>
        <td><a href="manage_cancellations.php?process_id=<?php echo $row['id']; ?>">Mark as Processed</a></td>
    </tr>
    <?php endwhile; ?>
</table>