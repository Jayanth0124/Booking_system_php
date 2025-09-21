<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'officer') { header("Location: login.php"); exit(); }
$officer_id = $_SESSION['related_id'];
$complaints = $conn->query("SELECT c.*, cat.category_name FROM complaints c JOIN complaint_categories cat ON c.category_id = cat.id WHERE c.officer_id = $officer_id ORDER BY c.status, c.submitted_at DESC");
?>
<a href="officer_dashboard.php">Back to Dashboard</a>
<h3>Assigned Complaints</h3>
<table border="1">
    <tr><th>ID</th><th>Title</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $complaints->fetch_assoc()): ?>
    <tr>
        <td>#<?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['complaint_title']); ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><a href="update_complaint.php?id=<?php echo $row['id']; ?>">Update Status/Proof</a></td>
    </tr>
    <?php endwhile; ?>
</table>