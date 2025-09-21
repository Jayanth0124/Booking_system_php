<?php
include 'db_connect.php';

// Ensure the user is logged in and is an administrator.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle deleting a user account.
if (isset($_GET['delete_id'])) {
    $user_id_to_delete = $_GET['delete_id'];

    // This requires deleting from two tables, so we use a transaction.
    $conn->begin_transaction();
    try {
        // First, get the related complainant ID before deleting the user.
        $stmt_get = $conn->prepare("SELECT related_id FROM users WHERE id = ? AND role = 'user'");
        $stmt_get->bind_param("i", $user_id_to_delete);
        $stmt_get->execute();
        $result = $stmt_get->get_result();
        
        if ($user_data = $result->fetch_assoc()) {
            $complainant_id_to_delete = $user_data['related_id'];

            // Delete from the 'users' table (login info).
            $stmt_user = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt_user->bind_param("i", $user_id_to_delete);
            $stmt_user->execute();

            // Delete from the 'complainants' table (profile info).
            $stmt_comp = $conn->prepare("DELETE FROM complainants WHERE id = ?");
            $stmt_comp->bind_param("i", $complainant_id_to_delete);
            $stmt_comp->execute();
        }
        
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        // Handle error, e.g., log it or show a message.
    }
    header("Location: manage_users.php");
    exit();
}

// Fetch all user accounts with their profile details.
$users_result = $conn->query("
    SELECT u.id, c.full_name, c.phone_number, u.email
    FROM users u
    JOIN complainants c ON u.related_id = c.id
    WHERE u.role = 'user'
    ORDER BY c.full_name
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
</head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>Manage User (Complainant) Accounts</h2>

    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $users_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                <td>
                    <a href="?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to permanently delete this user and all their complaints?');">
                        Delete User
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>