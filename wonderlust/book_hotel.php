<?php
include 'db_connect.php';

// 1. Ensure the user is logged in to make a booking.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. This block runs when the user submits their booking details.
if (isset($_POST['confirm_booking'])) {
    $user_id = $_SESSION['user_id'];
    $hotel_id = $_POST['hotel_id'];
    $check_in = $_POST['check_in_date'];
    $check_out = $_POST['check_out_date'];
    $num_guests = $_POST['num_guests'];

    // Fetch hotel details to calculate price and check availability.
    $hotel_stmt = $conn->prepare("SELECT price_per_night, available_rooms FROM hotels WHERE id = ?");
    $hotel_stmt->bind_param("i", $hotel_id);
    $hotel_stmt->execute();
    $hotel = $hotel_stmt->get_result()->fetch_assoc();

    // Calculate total price.
    $date1 = new DateTime($check_in);
    $date2 = new DateTime($check_out);
    $interval = $date1->diff($date2);
    $nights = $interval->days;
    $total_price = $nights * $hotel['price_per_night'];

    // Simplified availability check.
    // This counts how many bookings overlap with the requested dates.
    $booking_check = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE hotel_id = ? AND status = 'Confirmed' AND check_out_date > ? AND check_in_date < ?");
    $booking_check->bind_param("iss", $hotel_id, $check_in, $check_out);
    $booking_check->execute();
    $overlapping_bookings = $booking_check->get_result()->fetch_assoc()['count'];
    
    if ($overlapping_bookings >= $hotel['available_rooms']) {
        echo "<b>Sorry, no rooms are available for the selected dates. Please try different dates.</b>";
    } else {
        // If rooms are available, create the booking.
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, hotel_id, check_in_date, check_out_date, num_guests, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissid", $user_id, $hotel_id, $check_in, $check_out, $num_guests, $total_price);
        
        if ($stmt->execute()) {
            header("Location: my_bookings.php"); // Redirect to their bookings page
            exit();
        } else {
            echo "Error: Could not complete your booking.";
        }
    }
}

// 3. This block runs when the page first loads.
if (!isset($_GET['hotel_id'])) {
    header("Location: index.php");
    exit();
}
$hotel_id = $_GET['hotel_id'];
$hotel = $conn->query("SELECT * FROM hotels WHERE id = $hotel_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Hotel</title>
</head>
<body>
    <a href="destination_details.php?id=<?php echo $hotel['destination_id']; ?>">← Back to Destination Details</a>
    <h2>Book Your Stay at <?php echo htmlspecialchars($hotel['hotel_name']); ?></h2>

    <p><strong>Price per night:</strong> ₹<?php echo number_format($hotel['price_per_night'], 2); ?></p>

    <form method="post">
        <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
        
        Check-in Date:
        <input type="date" name="check_in_date" required><br><br>
        
        Check-out Date:
        <input type="date" name="check_out_date" required><br><br>
        
        Number of Guests:
        <input type="number" name="num_guests" value="1" min="1" required><br><br>
        
        <button type="submit" name="confirm_booking">Confirm Booking</button>
    </form>
</body>
</html>