<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        // 1. Get fare and check seat availability again
        $route_id = $_POST['route_id'];
        $class_id = $_POST['class_id'];
        $journey_date = $_POST['journey_date'];
        $passengers = $_POST['passengers'];
        $num_passengers = count($passengers['name']);

        $stmt = $conn->prepare("SELECT fare FROM fares WHERE route_id = ? AND class_id = ?");
        $stmt->bind_param("ii", $route_id, $class_id);
        $stmt->execute();
        $fare = $stmt->get_result()->fetch_assoc()['fare'];
        $total_fare = $fare * $num_passengers;

        // 2. Create Booking and generate PNR
        $pnr = "PNR" . time() . rand(100, 999);
        $stmt = $conn->prepare("INSERT INTO bookings (pnr_number, user_id, route_id, class_id, journey_date, num_passengers, total_fare) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siiisis", $pnr, $_SESSION['user_id'], $route_id, $class_id, $journey_date, $num_passengers, $total_fare);
        $stmt->execute();
        $booking_id = $conn->insert_id;

        // 3. Add passengers
        $stmt = $conn->prepare("INSERT INTO passengers (booking_id, passenger_name, age, gender) VALUES (?, ?, ?, ?)");
        for ($i = 0; $i < $num_passengers; $i++) {
            $stmt->bind_param("isis", $booking_id, $passengers['name'][$i], $passengers['age'][$i], $passengers['gender'][$i]);
            $stmt->execute();
        }

        // 4. Update seat availability
        $stmt = $conn->prepare("UPDATE seat_availability SET booked_seats = booked_seats + ? WHERE route_id = ? AND class_id = ? AND journey_date = ?");
        $stmt->bind_param("iiis", $num_passengers, $route_id, $class_id, $journey_date);
        $stmt->execute();

        $conn->commit();
        header("Location: confirmation.php?pnr=" . $pnr);
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Booking failed: " . $e->getMessage();
    }
}
?>
<a href="index.php">Home</a>
<h3>Passenger Details</h3>
<form method="post">
    <input type="hidden" name="route_id" value="<?php echo $_GET['route_id']; ?>">
    <input type="hidden" name="class_id" value="<?php echo $_GET['class_id']; ?>">
    <input type="hidden" name="journey_date" value="<?php echo $_GET['date']; ?>">
    <h4>Passenger 1</h4>
    Name: <input type="text" name="passengers[name][]" required><br>
    Age: <input type="number" name="passengers[age][]" required><br>
    Gender: <select name="passengers[gender][]"><option value="Male">Male</option><option value="Female">Female</option></select>
    <hr>
    <button type="submit">Confirm and Pay (Simulated)</button>
</form>