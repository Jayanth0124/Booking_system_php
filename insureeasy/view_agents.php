<?php
include 'db_connect.php';

// 1. Ensure the user is logged in and is an administrator.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Fetch all agent details by joining the 'agents' and 'users' tables.
// This gets the profile info and the login/status info in one query.
$agents_result = $conn->query("
    SELECT 
        a.full_name, 
        a.phone_number, 
        u.email, 
        u.is_approved 
    FROM agents a 
    JOIN users u ON a.id = u.related_id 
    WHERE u.role = 'agent'
    ORDER BY a.full_name
");

?>
<!DOCTYPE html>
<html>
<head>
    <title>View All Agents</title>
</head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>All Agent Details</h2>

    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Phone Number</th>
                <th>Login Email</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($agents_result->num_rows > 0): ?>
                <?php while ($agent = $agents_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($agent['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($agent['phone_number']); ?></td>
                    <td><?php echo htmlspecialchars($agent['email']); ?></td>
                    <td>
                        <?php echo $agent['is_approved'] ? '<b>Approved</b>' : 'Pending'; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No agents have registered yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>