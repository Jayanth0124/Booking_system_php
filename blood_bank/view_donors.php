<?php
include 'db_connect.php';

// Ensure the user is logged in and is an administrator.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all donor details from the database.
$donors_result = $conn->query("SELECT * FROM donors ORDER BY full_name");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View All Donors</title>
</head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>All Registered Donors</h2>

    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Blood Group</th>
                <th>Phone Number</th>
                <th>City</th>
                <th>Last Donated</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($donors_result->num_rows > 0): ?>
                <?php while ($donor = $donors_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($donor['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($donor['blood_group']); ?></td>
                    <td><?php echo htmlspecialchars($donor['phone_number']); ?></td>
                    <td><?php echo htmlspecialchars($donor['city']); ?></td>
                    <td><?php echo $donor['last_donation_date'] ? htmlspecialchars($donor['last_donation_date']) : 'N/A'; ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No donors have registered yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>