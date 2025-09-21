<?php
include 'db_connect.php';

// 1. Security Check: Ensure the user is a logged-in driver.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
    header("Location: login.php");
    exit();
}

// 2. Check if a route ID was provided in the URL.
if (!isset($_GET['route_id'])) {
    echo "No route specified.";
    exit();
}
$route_id = $_GET['route_id'];

// 3. This block runs when the driver submits the form with updated statuses.
if (isset($_POST['update_statuses'])) {
    $statuses = $_POST['statuses'];
    $current_datetime = date("Y-m-d H:i:s");

    // Prepare a statement to update the bin status and last cleared date.
    $stmt = $conn->prepare("UPDATE bins SET status = ?, last_cleared_date = ? WHERE id = ?");

    // Loop through each submitted status and update the database.
    foreach ($statuses as $bin_id => $status) {
        $stmt->bind_param("ssi", $status, $current_datetime, $bin_id);
        $stmt->execute();
    }
    echo "<p><strong>Bin statuses updated successfully!</strong></p>";
}

// 4. Fetch all bins for the specified route to display on the page.
$bins_result = $conn->query("
    SELECT b.id, b.bin_location_name, b.status 
    FROM bins b 
    JOIN route_bins rb ON b.id = rb.bin_id 
    WHERE rb.route_id = $route_id 
    ORDER BY rb.collection_order ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Bin Status</title>
</head>
<body>
    <a href="driver_dashboard.php">← Back to Dashboard</a>
    <h2>Update Garbage Load (Bin Status)</h2>
    <p>For each bin on your route, set the new status after collection.</p>

    <form method="post">
        <table border="1" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Bin Location</th>
                    <th>Current Status</th>
                    <th>Set New Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($bin = $bins_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($bin['bin_location_name']); ?></td>
                    <td><?php echo htmlspecialchars($bin['status']); ?></td>
                    <td>
                        <select name="statuses[<?php echo $bin['id']; ?>]">
                            <option value="Empty" <?php if($bin['status']=='Empty') echo 'selected'; ?>>Empty</option>
                            <option value="Half-full" <?php if($bin['status']=='Half-full') echo 'selected'; ?>>Half-full</option>
                            <option value="Full" <?php if($bin['status']=='Full') echo 'selected'; ?>>Full</option>
                            <option value="Overflowing" <?php if($bin['status']=='Overflowing') echo 'selected'; ?>>Overflowing</option>
                        </select>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <br>
        <button type="submit" name="update_statuses" style="font-size: 1.2em; padding: 10px;">Update All Statuses</button>
    </form>
</body>
</html>