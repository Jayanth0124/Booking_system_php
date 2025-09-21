<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
$bins = $conn->query("SELECT * FROM bins ORDER BY status DESC");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h2>Garbage Bin Status Report</h2>
<table border="1">
    <tr><th>Location Name</th><th>Status</th><th>Last Cleared</th></tr>
    <?php while ($bin = $bins->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($bin['bin_location_name']); ?></td>
        <td><b><?php echo htmlspecialchars($bin['status']); ?></b></td>
        <td><?php echo $bin['last_cleared_date'] ?? 'Never'; ?></td>
    </tr>
    <?php endwhile; ?>
</table>