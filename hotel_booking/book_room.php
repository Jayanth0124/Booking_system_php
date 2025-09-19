<?php
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $user_id = $_SESSION['user_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, check_in, check_out) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $room_id, $check_in, $check_out);
    
    if ($stmt->execute()) {
        echo "<h1>Booking Successful!</h1>";
        echo "<p>Your room has been booked from $check_in to $check_out.</p>";
        echo '<a href="my_bookings.php">View My Bookings</a>';
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    header("Location: index.php");
}
?>