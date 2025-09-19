<?php
include 'db_connect.php';

// 1. Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// 2. Handle Ticket Cancellation Request
if (isset($_GET['cancel_pnr'])) {
    $pnr_to_cancel = $_GET['cancel_pnr'];
    
    // Begin a transaction to ensure data integrity
    $conn->begin_transaction();
    
    try {
        // Find the booking to ensure it belongs to the user and is confirmed
        $stmt = $conn->prepare("SELECT * FROM bookings WHERE pnr_number = ? AND user_id = ? AND status = 'Confirmed'");
        $stmt->bind_param("si", $pnr_to_cancel, $user_id);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        
        if ($booking) {
            // a. Update the booking status to 'Cancelled'
            $update_booking = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
            $update_booking->bind_param("i", $booking['id']);
            $update_booking->execute();
            
            // b. Free up the booked seats
            $update_seats = $conn->prepare("UPDATE seat_availability SET booked_seats = booked_seats - ? WHERE route_id = ? AND class_id = ? AND journey_date = ?");
            $update_seats->bind_param("iiis", $booking['num_passengers'], $booking['route_id'], $booking['class_id'], $booking['journey_date']);
            $update_seats->execute();
            
            // c. Create a refund request for the admin (e.g., 80% refund)
            $refund_percentage = 0.80; 
            $refund_amount = $booking['total_fare'] * $refund_percentage;
            $insert_refund = $conn->prepare("INSERT INTO refunds (booking_id, refund_amount, status) VALUES (?, ?, 'Pending')");
            $insert_refund->bind_param("id", $booking['id'], $refund_amount);
            $insert_refund->execute();
            
            // If all queries succeed, commit the transaction
            $conn->commit();
            $message = "Success: Your ticket (PNR: " . htmlspecialchars($pnr_to_cancel) . ") has been cancelled. A refund request has been sent to the admin.";
        } else {
            // Rollback if the ticket is not valid for cancellation
            $conn->rollback();
            $message = "Error: Invalid PNR or ticket cannot be cancelled.";
        }
    } catch (Exception $e) {
        // Rollback on any error
        $conn->rollback();
        $message = "An error occurred: " . $e->getMessage();
    }
}


// 3. Fetch all bookings for the current user
$stmt = $conn->prepare("
    SELECT 
        b.pnr_number, b.journey_date, b.num_passengers, b.total_fare, b.status,
        t.train_name, t.train_number,
        r.origin_station, r.destination_station,
        c.class_name
    FROM bookings b
    JOIN routes r ON b.route_id = r.id
    JOIN trains t ON r.train_id = t.id
    JOIN classes c ON b.class_id = c.id
    WHERE b.user_id = ?
    ORDER BY b.journey_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
</head>
<body>
    <a href="index.php">Back to Home</a>
    <h2>My Bookings</h2>

    <?php if ($message): ?>
        <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
    <?php endif; ?>

    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>PNR</th>
                <th>Train</th>
                <th>Route</th>
                <th>Journey Date</th>
                <th>Class</th>
                <th>Passengers</th>
                <th>Total Fare</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['pnr_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['train_name'] . ' (' . $row['train_number'] . ')'); ?></td>
                    <td><?php echo htmlspecialchars($row['origin_station'] . ' to ' . $row['destination_station']); ?></td>
                    <td><?php echo htmlspecialchars($row['journey_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['num_passengers']); ?></td>
                    <td>₹<?php echo htmlspecialchars($row['total_fare']); ?></td>
                    <td><b><?php echo htmlspecialchars($row['status']); ?></b></td>
                    <td>
                        <?php if ($row['status'] == 'Confirmed'): ?>
                            <a href="my_bookings.php?cancel_pnr=<?php echo $row['pnr_number']; ?>" onclick="return confirm('Are you sure you want to cancel this ticket?');">
                                Cancel Ticket
                            </a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">You have no bookings.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>