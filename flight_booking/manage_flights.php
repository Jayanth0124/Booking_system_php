<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// ## LOGIC FOR ADDING/DELETING ##

// Add Airline
if (isset($_POST['add_airline'])) {
    $stmt = $conn->prepare("INSERT INTO airlines (airline_name) VALUES (?)");
    $stmt->bind_param("s", $_POST['airline_name']);
    $stmt->execute();
}

// Add Flight
if (isset($_POST['add_flight'])) {
    $stmt = $conn->prepare("INSERT INTO flights (airline_id, flight_number) VALUES (?, ?)");
    $stmt->bind_param("is", $_POST['airline_id'], $_POST['flight_number']);
    $stmt->execute();
}

// Add Schedule
if (isset($_POST['add_schedule'])) {
    $stmt = $conn->prepare("INSERT INTO schedules (flight_id, origin_airport, destination_airport, departure_time, arrival_time) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $_POST['flight_id'], $_POST['origin_airport'], $_POST['destination_airport'], $_POST['departure_time'], $_POST['arrival_time']);
    $stmt->execute();
}

// Delete logic
if (isset($_GET['delete'])) {
    $table = $_GET['table'];
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_flights.php");
    exit();
}

// ## FETCH DATA FOR DISPLAY ##
$airlines = $conn->query("SELECT * FROM airlines ORDER BY airline_name");
$flights = $conn->query("SELECT f.*, a.airline_name FROM flights f JOIN airlines a ON f.airline_id = a.id ORDER BY a.airline_name, f.flight_number");
$schedules = $conn->query("SELECT s.*, a.airline_name, f.flight_number FROM schedules s JOIN flights f ON s.flight_id = f.id JOIN airlines a ON f.airline_id = a.id ORDER BY s.departure_time");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Flights & Schedules</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h1>Manage Flights & Schedules</h1>

    <hr>
    <h2>Airlines</h2>
    <form method="post">
        <input type="text" name="airline_name" placeholder="New Airline Name" required>
        <button type="submit" name="add_airline">Add Airline</button>
    </form>
    <table border="1">
        <?php mysqli_data_seek($airlines, 0); while ($row = $airlines->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['airline_name']); ?></td>
            <td><a href="?delete=true&table=airlines&id=<?php echo $row['id']; ?>" onclick="return confirm('Sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <hr>
    <h2>Flights</h2>
    <form method="post">
        <select name="airline_id" required>
            <option value="">-- Select Airline --</option>
            <?php mysqli_data_seek($airlines, 0); while ($row = $airlines->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['airline_name']); ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="flight_number" placeholder="New Flight Number" required>
        <button type="submit" name="add_flight">Add Flight</button>
    </form>
    <table border="1">
         <?php while ($row = $flights->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['airline_name'] . ' - ' . $row['flight_number']); ?></td>
            <td><a href="?delete=true&table=flights&id=<?php echo $row['id']; ?>" onclick="return confirm('Sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
    
    <hr>
    <h2>Schedules</h2>
    <form method="post">
        <select name="flight_id" required>
            <option value="">-- Select Flight --</option>
            <?php mysqli_data_seek($flights, 0); while ($row = $flights->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['airline_name'] . ' - ' . $row['flight_number']); ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="origin_airport" placeholder="Origin (e.g., MAA)" required>
        <input type="text" name="destination_airport" placeholder="Destination (e.g., DEL)" required>
        <input type="datetime-local" name="departure_time" required>
        <input type="datetime-local" name="arrival_time" required>
        <button type="submit" name="add_schedule">Add Schedule</button>
    </form>
    <table border="1">
        <tr><th>Flight</th><th>Route</th><th>Departure</th><th>Arrival</th><th>Action</th></tr>
        <?php while ($row = $schedules->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['airline_name'] . ' - ' . $row['flight_number']); ?></td>
            <td><?php echo htmlspecialchars($row['origin_airport'] . ' to ' . $row['destination_airport']); ?></td>
            <td><?php echo $row['departure_time']; ?></td>
            <td><?php echo $row['arrival_time']; ?></td>
            <td><a href="?delete=true&table=schedules&id=<?php echo $row['id']; ?>" onclick="return confirm('Sure?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>