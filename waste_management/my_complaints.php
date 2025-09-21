<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'public') { header("Location: login.php"); exit(); }
$complaints = $conn->query("SELECT * FROM complaints WHERE public_user_id = ".$_SESSION['related_id']." ORDER BY submitted_at DESC");
?>
<a href="public_dashboard.php">Back to Dashboard</a>
<h3>My Complaint History</h3>
<table border="1">
    <tr><th>ID</th><th>Complaint</th><th>Submitted On</th><th>Status</th></tr>
    <?php while ($row = $complaints->fetch_assoc()): ?>
    <tr>
        <td>#<?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['complaint_text']); ?></td>
        <td><?php echo $row['submitted_at']; ?></td>
        <td><?php echo $row['status']; ?></td>
    </tr>
    <?php endwhile; ?>
</table> 