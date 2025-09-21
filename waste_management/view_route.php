<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') { header("Location: login.php"); exit(); }
$route_id = $_GET['route_id'];
$bins_result = $conn->query("SELECT b.* FROM route_bins rb JOIN bins b ON rb.bin_id = b.id WHERE rb.route_id = $route_id ORDER BY rb.collection_order ASC");
// Generate Google Maps URL
$maps_url = "https://www.google.com/maps/dir/";
$waypoints = [];
while ($bin = $bins_result->fetch_assoc()) {
    $waypoints[] = $bin['latitude'] . "," . $bin['longitude'];
}
$maps_url .= implode("/", $waypoints);
mysqli_data_seek($bins_result, 0); // Reset pointer
?>
<a href="driver_dashboard.php">Back to Dashboard</a>
<h3>Route Details</h3>
<a href="<?php echo $maps_url; ?>" target="_blank" style="font-size:1.2em">Open Route in Google Maps</a>
<h4>Bins to be collected (in order):</h4>
<ol>
    <?php while ($bin = $bins_result->fetch_assoc()): ?>
    <li><?php echo htmlspecialchars($bin['bin_location_name']); ?> (Status: <?php echo $bin['status']; ?>)</li>
    <?php endwhile; ?>
</ol>