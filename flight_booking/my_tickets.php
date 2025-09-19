<?php
include 'db_connect.php';

// 1. First, ensure the user is logged in. If not, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Fetch all bookings for the currently logged-in user.
// We join multiple tables to get all the necessary flight details.
$stmt = $conn->prepare("
    SELECT 
        b.booking_ref, 
        b.status,
        s.departure_time,
        s.origin_airport,
        s.destination_airport,
        a.airline_name,
        f.flight_number
    FROM bookings b
    JOIN schedules s ON b.schedule_id = s.id
    JOIN flights f ON s.flight_id = f.id
    JOIN airlines a ON f.airline_id = a.id
    WHERE b.user_id = ?
    ORDER BY s.departure_time DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Tickets</title>
</head>
<body>
    <a href="index.php">Back to Home</a>
    <h2>My Tickets</h2>

    <p>Here is a list of all your flight reservations.</p>

    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Booking Reference</th>
                <th>Airline</th>
                <th>Flight</th>
                <th>Route</th>
                <th>Departure</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><b><?php echo htmlspecialchars($row['booking_ref']); ?></b></td>
                    <td><?php echo htmlspecialchars($row['airline_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['flight_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['origin_airport'] . ' → ' . $row['destination_airport']); ?></td>
                    <td><?php echo htmlspecialchars(date('d M Y, g:i A', strtotime($row['departure_time']))); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <a href="view_ticket.php?ref=<?php echo $row['booking_ref']; ?>">View/Print</a>
                        
                        <?php // Only show the 'Cancel' link if the booking is confirmed. ?>
                        <?php if ($row['status'] == 'Confirmed'): ?>
                            | 
                            <a href="cancel_ticket.php?ref=<?php echo $row['booking_ref']; ?>" onclick="return confirm('Are you sure you want to cancel this ticket?');">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">You have not booked any tickets yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>