<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
if (isset($_GET['approve_id'])) {
    $stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE id = ? AND role = 'hospital'");
    $stmt->bind_param("i", $_GET['approve_id']);
    $stmt->execute();
}
$pending_hospitals = $conn->query("SELECT u.id, h.hospital_name, h.city, u.email FROM users u JOIN hospitals h ON u.related_id = h.id WHERE u.is_approved = 0 AND u.role = 'hospital'");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Pending Hospital Approvals</h3>
<table border="1">
    <tr><th>Name</th><th>City</th><th>Email</th><th>Action</th></tr>
    <?php while ($hosp = $pending_hospitals->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($hosp['hospital_name']); ?></td>
        <td><?php echo htmlspecialchars($hosp['city']); ?></td>
        <td><?php echo htmlspecialchars($hosp['email']); ?></td>
        <td><a href="?approve_id=<?php echo $hosp['id']; ?>">Approve</a></td>
    </tr>
    <?php endwhile; ?>
</table>