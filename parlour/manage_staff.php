<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] == 'customer') {
    header("Location: index.php");
    exit();
}

// Handle Add & Update
if (isset($_POST['save_staff'])) {
    $name = $_POST['staff_name'];
    $specialty = $_POST['specialty'];
    $id = $_POST['id'];

    if (empty($id)) {
        $stmt = $conn->prepare("INSERT INTO staff (staff_name, specialty) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $specialty);
    } else {
        $stmt = $conn->prepare("UPDATE staff SET staff_name=?, specialty=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $specialty, $id);
    }
    $stmt->execute();
    header("Location: manage_staff.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete_id']);
    $stmt->execute();
    header("Location: manage_staff.php");
    exit();
}

// Fetch a staff member for editing
$staff_to_edit = ['id' => '', 'staff_name' => '', 'specialty' => ''];
if (isset($_GET['edit_id'])) {
    $stmt = $conn->prepare("SELECT * FROM staff WHERE id = ?");
    $stmt->bind_param("i", $_GET['edit_id']);
    $stmt->execute();
    $staff_to_edit = $stmt->get_result()->fetch_assoc();
}

$staff_members = $conn->query("SELECT * FROM staff ORDER BY staff_name");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Staff</title></head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h3><?php echo empty($staff_to_edit['id']) ? 'Add New Staff' : 'Edit Staff'; ?></h3>
    <form method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($staff_to_edit['id']); ?>">
        Name: <input type="text" name="staff_name" value="<?php echo htmlspecialchars($staff_to_edit['staff_name']); ?>" required><br>
        Specialty: <input type="text" name="specialty" value="<?php echo htmlspecialchars($staff_to_edit['specialty']); ?>"><br>
        <button type="submit" name="save_staff">Save Staff</button>
    </form>
    <hr>
    <h3>Current Staff</h3>
    <table border="1">
        <tr><th>Name</th><th>Specialty</th><th>Actions</th></tr>
        <?php while ($row = $staff_members->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
            <td><?php echo htmlspecialchars($row['specialty']); ?></td>
            <td>
                <a href="?edit_id=<?php echo $row['id']; ?>">Edit</a> | 
                <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>