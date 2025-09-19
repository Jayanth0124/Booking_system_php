<?php
include 'db_connect.php';

// 1. Ensure the user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if an appointment ID was provided in the URL.
if (!isset($_GET['id'])) {
    $message = "Error: No appointment ID specified.";
} else {
    $appointment_id = $_GET['id'];
    $customer_id = $_SESSION['user_id'];

    // 3. Update the appointment status to 'Cancelled'.
    // CRUCIAL: We include "AND customer_id = ?" to ensure a user can only cancel their own appointments.
    $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ? AND customer_id = ?");
    $stmt->bind_param("ii", $appointment_id, $customer_id);
    $stmt->execute();

    // 4. Check if the update was successful.
    if ($stmt->affected_rows > 0) {
        $message = "Success! Your appointment has been cancelled.";
    } else {
        $message = "Error: Could not cancel the appointment. It may not exist or does not belong to you.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cancel Appointment</title>
</head>
<body>

    <div style="border: 1px solid #ccc; padding: 20px; text-align: center; max-width: 500px; margin: 40px auto;">
        <h2>Cancellation Status</h2>
        <p><?php echo $message; ?></p>
        <hr>
        <a href="my_appointments.php">← Go Back to My Appointments</a>
    </div>

</body>
</html>