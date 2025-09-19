<?php
include 'db_connect.php';
$ref = $_GET['ref'];
// Fetch booking and passenger details
// In a real app, you would fetch much more detail and format it nicely
$stmt = $conn->prepare("SELECT * FROM bookings WHERE booking_ref = ?");
$stmt->bind_param("s", $ref);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
?>
<h1>E-Ticket</h1>
<p><strong>Booking Reference:</strong> <?php echo htmlspecialchars($booking['booking_ref']); ?></p>
<p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>
<button onclick="window.print()">Print Ticket</button>
<a href="my_tickets.php">Back to My Tickets</a>