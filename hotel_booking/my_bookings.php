<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit();
}

// Handle cancellation
if (isset($_GET['cancel_id'])) {
    $booking_id = $_GET['cancel_id'];
    // Check if cancellation is allowed (more than 3 days before check-in)
    $stmt = $conn->prepare("SELECT check_in FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($booking = $result->fetch_assoc()) {
        $check_in_time = strtotime($booking['check_in']);
        if (($check_in_time - time()) / (60 * 60 * 24) > 3) {
            $update_stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
            $update_stmt->bind_param("i", $booking_id);
            $update_stmt->execute();
        } else {
            echo "<script>alert('Cancellation is only allowed more than 3 days before check-in.');</script>";
        }
    }
}

$stmt = $conn->prepare("SELECT b.id, r.room_type, b.check_in, b.check_out, b.status FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.user_id = ? ORDER BY b.check_in DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<a href="index.php">Back to Home</a>
<h3>My Bookings</h3>
<table border="1">
    <tr><th>Booking ID</th><th>Room Type</th><th>Check-in</th><th>Check-out</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['room_type']); ?></td>
        <td><?php echo $row['check_in']; ?></td>
        <td><?php echo $row['check_out']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td>
            <?php
            $can_modify = (strtotime($row['check_in']) - time()) / (60 * 60 * 24) > 3;
            if ($row['status'] == 'Confirmed' && $can_modify):
            ?>
                <a href="my_bookings.php?cancel_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Cancel</a>
            <?php else: echo 'N/A'; endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>