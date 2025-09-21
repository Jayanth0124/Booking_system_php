<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') { header("Location: login.php"); exit(); }
$driver_id = $_SESSION['related_id'];
$today = date("Y-m-d");
$assignment = $conn->query("SELECT da.*, r.route_name FROM driver_assignments da JOIN routes r ON da.route_id = r.id WHERE da.driver_id = $driver_id AND da.assignment_date = '$today'")->fetch_assoc();
?>
<h1>Driver Dashboard</h1>
<h3>Daily Work Update for <?php echo $today; ?></h3>
<?php if ($assignment): ?>
    <p>Your assigned route for today is: <b><?php echo htmlspecialchars($assignment['route_name']); ?></b></p>
    <a href="view_route.php?route_id=<?php echo $assignment['route_id']; ?>" style="font-size:1.2em">View Route Details & Map</a><br><br>
    <a href="update_bin_status.php?route_id=<?php echo $assignment['route_id']; ?>" style="font-size:1.2em">Update Garbage Load</a>
<?php else: ?>
    <p>You have not been assigned a route for today.</p>
<?php endif; ?>
<hr>
<a href="logout.php">Logout</a>