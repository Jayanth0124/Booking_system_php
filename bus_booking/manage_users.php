<?php
include 'db_connect.php';
// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle Delete User
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    
    // Prevent admin from deleting their own account
    if ($id_to_delete != $_SESSION['user_id']) {
        // Note: In a real system, you might want to handle user's bookings before deleting.
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id_to_delete);
        $stmt->execute();
    }
    header("Location: manage_users.php"); // Redirect to clean the URL
    exit();
}

// Fetch all users
$result = $conn->query("SELECT * FROM users ORDER BY username");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>Registered Users</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['role']); ?></td>
            <td>
                <?php if ($row['role'] !== 'admin'): // Do not allow deleting other admins for simplicity ?>
                    <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>