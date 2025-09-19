<?php
include 'db_connect.php';

// 1. Ensure the user is logged in to book.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. This block runs when the user clicks the final "Confirm" button.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_SESSION['user_id'];
    $staff_id = $_POST['staff_id'];
    $service_id = $_POST['service_id'];
    $appointment_time = $_POST['appointment_time'];

    // Insert the appointment into the database with a "Pending Payment" status.
    $stmt = $conn->prepare("INSERT INTO appointments (customer_id, staff_id, service_id, appointment_time, status) VALUES (?, ?, ?, ?, 'Pending Payment')");
    $stmt->bind_param("iiis", $customer_id, $staff_id, $service_id, $appointment_time);
    
    if ($stmt->execute()) {
        $appointment_id = $conn->insert_id;
        // Redirect to a simulated payment page.
        header("Location: payment.php?appointment_id=" . $appointment_id);
        exit();
    } else {
        echo "Error: Could not create appointment.";
    }
}

// 3. This block runs when the page first loads to display the details.
// Get the details from the URL.
if (!isset($_GET['service_id']) || !isset($_GET['staff_id']) || !isset($_GET['time'])) {
    echo "Error: Missing appointment details.";
    exit();
}
$service_id = $_GET['service_id'];
$staff_id = $_GET['staff_id'];
$appointment_time = $_GET['time'];

// Fetch details from the database to show the user.
$service_stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$service_stmt->bind_param("i", $service_id);
$service_stmt->execute();
$service = $service_stmt->get_result()->fetch_assoc();

$staff_stmt = $conn->prepare("SELECT * FROM staff WHERE id = ?");
$staff_stmt->bind_param("i", $staff_id);
$staff_stmt->execute();
$staff = $staff_stmt->get_result()->fetch_assoc();

if (!$service || !$staff) {
    echo "Error: Invalid service or staff selected.";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Confirm Booking</title>
</head>
<body>
    <a href="booking.php?service_id=<?php echo $service_id; ?>">← Go Back</a>
    <h2>Confirm Your Appointment</h2>

    <div style="border: 1px solid #007bff; padding: 15px;">
        <h4>Please review your appointment details:</h4>
        <p><strong>Service:</strong> <?php echo htmlspecialchars($service['service_name']); ?></p>
        <p><strong>Stylist:</strong> <?php echo htmlspecialchars($staff['staff_name']); ?></p>
        <p><strong>Date & Time:</strong> <?php echo htmlspecialchars(date('l, F j, Y \a\t g:i A', strtotime($appointment_time))); ?></p>
        <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($service['price']); ?></p>

        <form method="post" action="confirm_booking.php">
            <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
            <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
            <input type="hidden" name="appointment_time" value="<?php echo $appointment_time; ?>">
            
            <button type="submit" style="font-size: 1.2em; padding: 10px;">Confirm & Proceed to Payment</button>
        </form>
    </div>
</body>
</html>