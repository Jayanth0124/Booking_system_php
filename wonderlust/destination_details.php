<?php
include 'db_connect.php';
if (!isset($_GET['id'])) { header("Location: index.php"); exit(); }
$dest_id = $_GET['id'];
$destination = $conn->query("SELECT * FROM destinations WHERE id = $dest_id")->fetch_assoc();
?>
<a href="index.php">← Back to Destinations</a>
<h1><?php echo htmlspecialchars($destination['destination_name']); ?></h1>
<p><?php echo htmlspecialchars($destination['description']); ?></p>
<hr>
<h3>What to do:</h3>
<ul>
    <li><a href="view_hotels.php?dest_id=<?php echo $dest_id; ?>">View Hotels & Accommodations</a></li>
    <li><a href="view_foods.php?dest_id=<?php echo $dest_id; ?>">View Food Details</a></li>
    <li><a href="view_routes.php?dest_id=<?php echo $dest_id; ?>">View Route & Itinerary Details</a></li>
</ul>