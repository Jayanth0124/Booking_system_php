// pnr_status.php
<?php
include 'db_connect.php';
$pnr = $_GET['pnr'];
$stmt = $conn->prepare("SELECT b.*, t.train_name, r.origin_station, r.destination_station FROM bookings b JOIN routes r ON b.route_id = r.id JOIN trains t ON r.train_id = t.id WHERE pnr_number = ?");
$stmt->bind_param("s", $pnr);
$stmt->execute();
$result = $stmt->get_result();
if ($booking = $result->fetch_assoc()) {
    echo "<h3>PNR Status for ".htmlspecialchars($pnr)."</h3>";
    echo "Train: ".htmlspecialchars($booking['train_name'])."<br>";
    echo "Route: ".htmlspecialchars($booking['origin_station'])." to ".htmlspecialchars($booking['destination_station'])."<br>";
    echo "Journey Date: ".$booking['journey_date']."<br>";
    echo "<b>Status: ".$booking['status']."</b>";
} else {
    echo "Invalid PNR Number.";
}
?>