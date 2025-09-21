<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
if (isset($_GET['approve_id'])) {
    $stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE id = ? AND role = 'director'");
    $stmt->bind_param("i", $_GET['approve_id']);
    $stmt->execute();
}
$pending_directors = $conn->query("SELECT u.id, d.company_name, d.contact_person, u.email FROM users u JOIN directors d ON u.related_id = d.id WHERE u.is_approved = 0 AND u.role = 'director'");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Pending Director Approvals</h3>
<table border="1">
    <tr><th>Company</th><th>Contact</th><th>Email</th><th>Action</th></tr>
    <?php while ($dir = $pending_directors->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($dir['company_name']); ?></td>
        <td><?php echo htmlspecialchars($dir['contact_person']); ?></td>
        <td><?php echo htmlspecialchars($dir['email']); ?></td>
        <td><a href="?approve_id=<?php echo $dir['id']; ?>">Approve</a></td>
    </tr>
    <?php endwhile; ?>
</table>