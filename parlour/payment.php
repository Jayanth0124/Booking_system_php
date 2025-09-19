<?php
include 'db_connect.php';

// 1. Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. This block runs when the "Pay Now" button is clicked.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];
    $customer_id = $_SESSION['user_id'];

    // Update the appointment status from 'Pending Payment' to 'Confirmed'.
    // We also check the customer_id to ensure a user can only confirm their own appointments.
    $stmt = $conn->prepare("UPDATE appointments SET status = 'Confirmed' WHERE id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $appointment_id, $customer_id);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        // If the update was successful, show a confirmation message.
        echo "<div style='border: 2px solid green; padding: 20px; text-align: center;'>";
        echo "<h1>✅ Payment Successful!</h1>";
        echo "<p>Your appointment is now confirmed.</p>";
        echo "<a href='my_appointments.php'>View My Appointments</a>";
        echo "</div>";
    } else {
        echo "Error: Could not confirm appointment or appointment not found.";
    }
    exit(); // Stop the script here after processing the payment.
}


// 3. This block runs when the page first loads.
// Get the appointment ID from the URL.
if (!isset($_GET['appointment_id'])) {
    echo "Error: No appointment specified.";
    exit();
}
$appointment_id = $_GET['appointment_id'];

// Fetch appointment details to display the amount.
$stmt = $conn->prepare("
    SELECT a.id, s.service_name, s.price 
    FROM appointments a 
    JOIN services s ON a.service_id = s.id 
    WHERE a.id = ? AND a.customer_id = ? AND a.status = 'Pending Payment'
");
$stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();

if (!$appointment) {
    echo "Error: This appointment is either already confirmed or invalid.";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Process Payment</title>
</head>
<body>
    <h2>Simulated Payment Gateway</h2>
    
    <div style="border: 1px solid #ccc; padding: 15px; max-width: 400px;">
        <h3>Payment Details</h3>
        <p><strong>Service:</strong> <?php echo htmlspecialchars($appointment['service_name']); ?></p>
        <p><strong>Amount to Pay:</strong> ₹<?php echo number_format($appointment['price'], 2); ?></p>
        
        <form method="post" action="payment.php">
            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
            <p>Clicking "Pay Now" will simulate a successful payment and confirm your booking.</p>
            <button type="submit" style="font-size: 1.2em; padding: 10px; background-color: #28a745; color: white; border: none;">Pay Now (Simulated)</button>
        </form>
    </div>
</body>
</html>