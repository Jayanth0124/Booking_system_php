<?php
include 'db_connect.php';
if (!isset($_GET['id'])) { header("Location: index.php"); exit(); }
$rest_id = $_GET['id'];

// Handle booking submission
if (isset($_POST['book_table'])) {
    if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
    
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reservation_time = "$date $time";
    $guests = $_POST['guests'];
    $table_type_id = $_POST['table_type_id'];
    
    // Simplified: Get table fee
    $table_info = $conn->query("SELECT base_booking_fee FROM table_types WHERE id = $table_type_id")->fetch_assoc();
    $cost = $table_info['base_booking_fee'];
    
    // Simplified availability check
    $booked_tables = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE table_type_id = $table_type_id AND reservation_time = '$reservation_time'")->fetch_assoc()['count'];
    $total_tables = $conn->query("SELECT quantity FROM table_types WHERE id = $table_type_id")->fetch_assoc()['quantity'];
    
    if ($booked_tables < $total_tables) {
        $stmt = $conn->prepare("INSERT INTO reservations (user_id, restaurant_id, table_type_id, reservation_time, num_guests, total_cost) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisis", $_SESSION['user_id'], $rest_id, $table_type_id, $reservation_time, $guests, $cost);
        $stmt->execute();
        header("Location: my_reservations.php");
        exit();
    } else {
        echo "<b>Sorry, that time slot is fully booked for the selected table type.</b>";
    }
}
$restaurant = $conn->query("SELECT * FROM restaurants WHERE id = $rest_id")->fetch_assoc();
$table_types = $conn->query("SELECT * FROM table_types WHERE restaurant_id = $rest_id");
?>
<a href="index.php">← Back to Restaurants</a>
<h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
<p><?php echo htmlspecialchars($restaurant['description']); ?></p>
<hr>
<h3>Book a Table</h3>
<form method="post">
    Date: <input type="date" name="date" required><br>
    Time: <input type="time" name="time" required step="1800"><br> Number of Guests: <input type="number" name="guests" required min="1"><br>
    Table Type: 
    <select name="table_type_id" required>
        <?php while($table = $table_types->fetch_assoc()): ?>
            <option value="<?php echo $table['id']; ?>">
                <?php echo htmlspecialchars($table['type_name'] . " (Max " . $table['capacity'] . " people)"); ?>
            </option>
        <?php endwhile; ?>
    </select><br>
    <button type="submit" name="book_table">Check Availability & Book</button>
</form>