<?php include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); } 
// Handle Add/Edit Bus Logic
if (isset($_POST['save_bus'])) {
    $bus_name = $_POST['bus_name'];
    $category_id = $_POST['category_id'];
    $total_seats = $_POST['total_seats'];
    if (empty($_POST['bus_id'])) {
        $stmt = $conn->prepare("INSERT INTO buses (bus_name, category_id, total_seats) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $bus_name, $category_id, $total_seats);
    } else {
        $stmt = $conn->prepare("UPDATE buses SET bus_name=?, category_id=?, total_seats=? WHERE id=?");
        $stmt->bind_param("siii", $bus_name, $category_id, $total_seats, $_POST['bus_id']);
    }
    $stmt->execute();
}
// Handle Add Schedule
if(isset($_POST['add_schedule'])) {
    $stmt = $conn->prepare("INSERT INTO schedules (bus_id, origin, destination, departure_time, arrival_time, price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssd", $_POST['bus_id'], $_POST['origin'], $_POST['destination'], $_POST['departure_time'], $_POST['arrival_time'], $_POST['price']);
    $stmt->execute();
}
$buses = $conn->query("SELECT b.*, c.name as category_name FROM buses b JOIN bus_categories c ON b.category_id = c.id");
$categories = $conn->query("SELECT * FROM bus_categories");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Add/Edit Bus</h3>
<form method="post">
    <input type="hidden" name="bus_id" value=""> 
    Bus Name: <input type="text" name="bus_name" required><br>
    Category: <select name="category_id" required>
        <?php while($cat = $categories->fetch_assoc()): ?>
            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
        <?php endwhile; ?>
    </select><br>
    Total Seats: <input type="number" name="total_seats" required><br>
    <button type="submit" name="save_bus">Save Bus</button>
</form>
<hr>
<h3>Manage Buses & Schedules</h3>
<table border="1">
    <tr><th>Bus Name</th><th>Category</th><th>Seats</th><th>Schedules</th><th>Add Schedule</th></tr>
    <?php while($bus = $buses->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($bus['bus_name']); ?></td>
        <td><?php echo htmlspecialchars($bus['category_name']); ?></td>
        <td><?php echo $bus['total_seats']; ?></td>
        <td>
            <ul>
            <?php
            $schedules = $conn->query("SELECT * FROM schedules WHERE bus_id = ".$bus['id']);
            while($sch = $schedules->fetch_assoc()) {
                echo "<li>".$sch['origin']." to ".$sch['destination']." at ".date('g:i A, d M Y', strtotime($sch['departure_time']))."</li>";
            }
            ?>
            </ul>
        </td>
        <td>
            <form method="post">
                <input type="hidden" name="bus_id" value="<?php echo $bus['id']; ?>">
                Origin: <input type="text" name="origin" required><br>
                Destination: <input type="text" name="destination" required><br>
                Departure: <input type="datetime-local" name="departure_time" required><br>
                Arrival: <input type="datetime-local" name="arrival_time" required><br>
                Price: <input type="text" name="price" required><br>
                <button type="submit" name="add_schedule">Add</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>