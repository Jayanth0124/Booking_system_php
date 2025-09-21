<?php
include 'db_connect.php';

// Ensure the user is logged in and is an administrator.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all applicants (users) with their details.
$users_result = $conn->query("
    SELECT a.full_name, a.phone_number, a.resume_path, u.email
    FROM applicants a
    JOIN users u ON a.id = u.related_id
    WHERE u.role = 'user'
    ORDER BY a.full_name
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View All Users</title>
</head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>All Registered Users (Job Seekers)</h2>

    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Resume</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users_result->num_rows > 0): ?>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
                    <td>
                        <?php if ($user['resume_path']): ?>
                            <a href="<?php echo htmlspecialchars($user['resume_path']); ?>" target="_blank">View Resume</a>
                        <?php else: ?>
                            Not Uploaded
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No users have registered yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>