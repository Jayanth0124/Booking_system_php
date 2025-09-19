<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schedule_id = $_POST['schedule_id'];
    $num_seats = $_POST['num_seats'];
    $user_id = $_SESSION['user_id'];
    // Get price from schedule
    $price_stmt = $conn->prepare("SELECT price FROM schedules WHERE id = ?");
    $price_stmt->bind_param("i", $schedule_id);
    $price_stmt->execute();
    $price_result = $price_stmt->get_result()->fetch_assoc();
    $total_price = $price_result['price'] * $num_seats;

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, schedule_id, num_seats, total_price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $user_id, $schedule_id, $num_seats, $total_price);
    if ($stmt->execute()) {
        echo "<h1>Booking Confirmed!</h1><p>You have successfully booked $num_seats seat(s).</p><a href='my_bookings.php'>View My Bookings</a>";
    } else {
        echo "Booking failed.";
    }
}
?>