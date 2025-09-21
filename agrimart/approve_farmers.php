<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// Handle approving a farmer
if (isset($_GET['approve_id'])) {
    // Note: In a real system, OTP verification should be confirmed before approval.
    // For this project, we are setting is_verified to 1 directly.
    $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE id = ? AND role = 'farmer'");
    $stmt->bind_param("i", $_GET['approve_id']);
    $stmt->execute();
    header("Location: approve_farmers.php");
    exit();
}

// Fetch farmers who have completed OTP but are not yet approved by admin (logic can be adapted)
// For simplicity, we show all unverified farmers
$pending_farmers = $conn->query("
    SELECT u.id, f.full_name, f.farm_name, f.phone_number, u.username
    FROM users u
    JOIN farmers f ON u.id = f.user_id
    WHERE u.is_verified = 0 AND u.role = 'farmer'
");
?>
<!DOCTYPE html>
<html>
<head><title>Approve Farmers</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>Pending Farmer Approvals</h2>
    <p>These farmers have registered but require manual approval to start selling.</p>

    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Farm Name</th>
                <th>Phone</th>
                <th>Username</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($farmer = $pending_farmers->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($farmer['full_name']); ?></td>
                <td><?php echo htmlspecialchars($farmer['farm_name']); ?></td>
                <td><?php echo htmlspecialchars($farmer['phone_number']); ?></td>
                <td><?php echo htmlspecialchars($farmer['username']); ?></td>
                <td>
                    <a href="?approve_id=<?php echo $farmer['id']; ?>">Approve</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>