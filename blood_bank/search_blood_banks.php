<?php
include 'db_connect.php';

if (!isset($_GET['city'])) {
    header("Location: index.php");
    exit();
}
$city = $_GET['city'];

// CORRECTED QUERY: This now joins with the 'users' table to check for approval status.
$stmt = $conn->prepare("
    SELECT h.* FROM hospitals h
    JOIN users u ON h.id = u.related_id
    WHERE u.role = 'hospital' AND u.is_approved = 1 AND h.city = ?
");
$stmt->bind_param("s", $city);
$stmt->execute();
$hospitals = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blood Bank Search Results</title>
</head>
<body>
    <a href="index.php">← Back to Search</a>
    <h2>Available Blood Banks in '<?php echo htmlspecialchars($city); ?>'</h2>
    
    <?php if ($hospitals->num_rows > 0): ?>
        <?php while ($hospital = $hospitals->fetch_assoc()): ?>
            <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
                <h3><?php echo htmlspecialchars($hospital['hospital_name']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($hospital['address'])); ?></p>
                
                <h4>Current Blood Stock (in Pints)</h4>
                <?php
                // Fetch stock for this specific hospital
                $stock_result = $conn->query("SELECT blood_group, quantity_pints FROM blood_stock WHERE hospital_id = " . $hospital['id']);
                if ($stock_result->num_rows > 0) {
                    echo "<table border='1'><tr>";
                    while ($stock = $stock_result->fetch_assoc()) {
                        echo "<td style='padding: 5px;'><b>" . htmlspecialchars($stock['blood_group']) . ":</b> " . htmlspecialchars($stock['quantity_pints']) . "</td>";
                    }
                    echo "</tr></table>";
                } else {
                    echo "<p>Stock information has not been updated by the hospital yet.</p>";
                }
                ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No approved blood banks found in this city.</p>
    <?php endif; ?>
</body>
</html>