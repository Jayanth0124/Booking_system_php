<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); }
if (isset($_GET['approve_id'])) {
    $stmt = $conn->prepare("UPDATE hospitals SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $_GET['approve_id']);
    $stmt->execute();
}
$hospitals = $conn->query("SELECT * FROM hospitals ORDER BY is_approved, hospital_name");
?>
<h3>Manage Hospitals</h3>
<table border="1">
    <tr><th>Name</th><th>City</th><th>Status</th><th>Action</th></tr>
    <?php while ($hospital = $hospitals->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($hospital['hospital_name']); ?></td>
        <td><?php echo htmlspecialchars($hospital['city']); ?></td>
        <td><?php echo $hospital['is_approved'] ? 'Approved' : 'Pending Approval'; ?></td>
        <td>
            <?php if (!$hospital['is_approved']): ?>
            <a href="?approve_id=<?php echo $hospital['id']; ?>">Approve</a>
            <?php else: echo 'N/A'; endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>