<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle processing a refund
if (isset($_GET['process_id'])) {
    $id_to_process = $_GET['process_id'];
    $stmt = $conn->prepare("UPDATE cancellations SET status = 'Processed' WHERE id = ?");
    $stmt->bind_param("i", $id_to_process);
    $stmt->execute();
    header("Location: manage_cancellations.php");
    exit();
}

// Fetch pending and processed cancellations
$pending_cancellations = $conn->query("SELECT c.*, b.booking_ref, u.username FROM cancellations c JOIN bookings b ON c.booking_id = b.id JOIN users u ON b.user_id = u.id WHERE c.status = 'Pending' ORDER BY c.requested_at DESC");
$processed_cancellations = $conn->query("SELECT c.*, b.booking_ref, u.username FROM cancellations c JOIN bookings b ON c.booking_id = b.id JOIN users u ON b.user_id = u.id WHERE c.status = 'Processed' ORDER BY c.requested_at DESC");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Cancellations</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h1>Manage Cancellations</h1>

    <hr>
    <h2>Pending Refunds</h2>
    <table border="1">
        <tr><th>Booking Ref</th><th>User</th><th>Refund Amount</th><th>Requested At</th><th>Action</th></tr>
        <?php while ($row = $pending_cancellations->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['booking_ref']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td>₹<?php echo htmlspecialchars($row['refund_amount']); ?></td>
            <td><?php echo $row['requested_at']; ?></td>
            <td><a href="?process_id=<?php echo $row['id']; ?>">Mark as Processed</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <hr>
    <h2>Processed Refunds</h2>
    <table border="1">
        <tr><th>Booking Ref</th><th>User</th><th>Refund Amount</th><th>Requested At</th></tr>
         <?php while ($row = $processed_cancellations->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['booking_ref']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td>₹<?php echo htmlspecialchars($row['refund_amount']); ?></td>
            <td><?php echo $row['requested_at']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>