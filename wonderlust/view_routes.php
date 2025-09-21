<?php
include 'db_connect.php';

// Ensure a destination ID is provided in the URL
if (!isset($_GET['dest_id'])) {
    header("Location: index.php");
    exit();
}
$dest_id = $_GET['dest_id'];

// Fetch the destination name to use as a title
$dest_stmt = $conn->prepare("SELECT destination_name FROM destinations WHERE id = ?");
$dest_stmt->bind_param("i", $dest_id);
$dest_stmt->execute();
$destination = $dest_stmt->get_result()->fetch_assoc();

// Fetch all routes linked to this destination
$route_stmt = $conn->prepare("SELECT * FROM routes WHERE destination_id = ?");
$route_stmt->bind_param("i", $dest_id);
$route_stmt->execute();
$routes = $route_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Routes in <?php echo htmlspecialchars($destination['destination_name']); ?></title>
</head>
<body>
    <a href="destination_details.php?id=<?php echo $dest_id; ?>">← Back to Destination Details</a>
    <h2>Routes & Itineraries for <?php echo htmlspecialchars($destination['destination_name']); ?></h2>

    <?php if ($routes->num_rows > 0): ?>
        <?php while ($route = $routes->fetch_assoc()): ?>
            <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; max-width: 600px;">
                <h3><?php echo htmlspecialchars($route['route_title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($route['description'])); ?></p>
                <?php if (!empty($route['map_link'])): ?>
                    <a href="<?php echo htmlspecialchars($route['map_link']); ?>" target="_blank">View on Map</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No route details have been added for this destination yet.</p>
    <?php endif; ?>
</body>
</html>