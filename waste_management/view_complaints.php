<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// Handle updating complaint status
if (isset($_POST['update_status'])) {
    $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['status'], $_POST['complaint_id']);
    $stmt->execute();
    header("Location: view_complaints.php");
    exit();
}

// Fetch all complaints with user details
$complaints = $conn->query("
    SELECT c.*, pu.full_name, pu.phone_number 
    FROM complaints c 
    JOIN public_users pu ON c.public_user_id = pu.id 
    ORDER BY c.submitted_at DESC
");
?>
<!DOCTYPE html>
<html>
<head><title>View Public Complaints</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>View Public Complaints</h2>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Complainant</th>
                <th>Phone</th>
                <th>Complaint Details</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $complaints->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['complaint_text'])); ?></td>
                <td><?php echo $row['submitted_at']; ?></td>
                <td><b><?php echo $row['status']; ?></b></td>
                <td>
                    <form method="post" style="margin:0;">
                        <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                        <select name="status">
                            <option value="Submitted" <?php if($row['status']=='Submitted') echo 'selected'; ?>>Submitted</option>
                            <option value="In Review" <?php if($row['status']=='In Review') echo 'selected'; ?>>In Review</option>
                            <option value="Resolved" <?php if($row['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
                        </select>
                        <button type="submit" name="update_status">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>