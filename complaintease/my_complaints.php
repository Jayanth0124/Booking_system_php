<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') { header("Location: login.php"); exit(); }
$complaints = $conn->query("SELECT c.*, cat.category_name FROM complaints c JOIN complaint_categories cat ON c.category_id = cat.id WHERE c.complainant_id = ".$_SESSION['related_id']." ORDER BY c.submitted_at DESC");
?>
<a href="user_dashboard.php">Back to Dashboard</a>
<h3>My Complaint History</h3>
<table border="1">
    <tr><th>ID</th><th>Title</th><th>Category</th><th>Submitted On</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $complaints->fetch_assoc()): ?>
    <tr>
        <td>#<?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['complaint_title']); ?></td>
        <td><?php echo htmlspecialchars($row['category_name']); ?></td>
        <td><?php echo $row['submitted_at']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><a href="view_complaint.php?id=<?php echo $row['id']; ?>">View Details</a></td>
    </tr>
    <?php endwhile; ?>
</table>