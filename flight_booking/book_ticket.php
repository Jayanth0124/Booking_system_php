<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // This is a simplified booking process. A real one would have more checks.
    $schedule_id = $_POST['schedule_id'];
    $class_id = $_POST['class_id'];
    $passengers = $_POST['passengers'];
    $num_passengers = count($passengers['name']);
    
    // Get fare and calculate total
    $stmt = $conn->prepare("SELECT fare FROM fares WHERE schedule_id = ? AND class_id = ?");
    $stmt->bind_param("ii", $schedule_id, $class_id);
    $stmt->execute();
    $fare = $stmt->get_result()->fetch_assoc()['fare'];
    $total_fare = $fare * $num_passengers;
    
    // Create Booking and generate reference number
    $booking_ref = "FL" . time() . rand(10, 99);
    $stmt = $conn->prepare("INSERT INTO bookings (booking_ref, user_id, schedule_id, class_id, num_passengers, total_fare) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiid", $booking_ref, $_SESSION['user_id'], $schedule_id, $class_id, $num_passengers, $total_fare);
    $stmt->execute();
    $booking_id = $conn->insert_id;
    
    // Add passengers to passengers table
    $stmt = $conn->prepare("INSERT INTO passengers (booking_id, passenger_name, passport_number) VALUES (?, ?, ?)");
    for ($i = 0; $i < $num_passengers; $i++) {
        $stmt->bind_param("iss", $booking_id, $passengers['name'][$i], $passengers['passport'][$i]);
        $stmt->execute();
    }
    
    // Update inventory
    $stmt = $conn->prepare("UPDATE inventory SET booked_seats = booked_seats + ? WHERE schedule_id = ? AND class_id = ?");
    $stmt->bind_param("iii", $num_passengers, $schedule_id, $class_id);
    $stmt->execute();
    
    // Simulate payment and redirect to confirmation
    header("Location: confirmation.php?ref=" . $booking_ref);
    exit();
}
?>
<h3>Enter Passenger Details</h3>
<form method="post">
    <input type="hidden" name="schedule_id" value="<?php echo $_GET['schedule_id']; ?>">
    <input type="hidden" name="class_id" value="<?php echo $_GET['class_id']; ?>">
    <h4>Passenger 1</h4>
    Name: <input type="text" name="passengers[name][]" required><br>
    Passport No: <input type="text" name="passengers[passport][]" required><br>
    <hr>
    <button type="submit">Proceed to Payment</button> </form>