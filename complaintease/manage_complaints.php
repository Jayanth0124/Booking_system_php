<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if (isset($_POST['assign_officer'])) {
    $stmt = $conn->prepare("UPDATE complaints SET officer_id = ?, status = 'In Progress' WHERE id = ?");
    $stmt->bind_param("ii", $_POST['officer_id'], $_POST['complaint_id']);
    $stmt->execute();
}
$complaints = $conn->query("SELECT c.*, cat.category_name, o.full_name as officer_name FROM complaints c LEFT JOIN officers o ON c.officer_id = o.id JOIN complaint_categories cat ON c.category_id = cat.id ORDER BY c.submitted_at DESC");
$officers = $conn->query("SELECT * FROM officers");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>All Complaints</h3>
<table border="1">
    <tr><th>ID</th><th>Title</th><th>Status</th><th>Assigned To</th><th>Assign</th></tr>
    <?php while ($row = $complaints->fetch_assoc()): ?>
    <tr>
        <td>#<?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['complaint_title']); ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><?php echo $row['officer_name'] ?? 'Not Assigned'; ?></td>
        <td>
            <?php if ($row['status'] == 'Pending'): ?>
            <form method="post">
                <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                <select name="officer_id" required>
                    <?php mysqli_data_seek($officers, 0); while ($off = $officers->fetch_assoc()): ?>
                    <option value="<?php echo $off['id']; ?>"><?php echo htmlspecialchars($off['full_name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="assign_officer">Assign</button>
            </form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>