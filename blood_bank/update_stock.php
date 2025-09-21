<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hospital') { header("Location: login.php"); exit(); }
$hospital_id = $_SESSION['related_id'];
if (isset($_POST['update_stock'])) {
    foreach ($_POST['quantity'] as $blood_group => $quantity) {
        // This query will insert a new row if it doesn't exist, or update the quantity if it does.
        $stmt = $conn->prepare("INSERT INTO blood_stock (hospital_id, blood_group, quantity_pints) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity_pints = VALUES(quantity_pints)");
        $stmt->bind_param("isi", $hospital_id, $blood_group, $quantity);
        $stmt->execute();
    }
    echo "<b>Stock updated successfully!</b>";
}
// Fetch current stock
$stock_result = $conn->query("SELECT * FROM blood_stock WHERE hospital_id = $hospital_id");
$current_stock = [];
while ($row = $stock_result->fetch_assoc()) {
    $current_stock[$row['blood_group']] = $row['quantity_pints'];
}
$blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
?>
<a href="hospital_dashboard.php">Back to Dashboard</a>
<h3>Update Blood Stock (in Pints)</h3>
<form method="post">
    <?php foreach ($blood_groups as $group): ?>
        <?php echo $group; ?>: <input type="number" name="quantity[<?php echo $group; ?>]" value="<?php echo $current_stock[$group] ?? 0; ?>" required><br>
    <?php endforeach; ?>
    <button type="submit" name="update_stock">Update All</button>
</form>