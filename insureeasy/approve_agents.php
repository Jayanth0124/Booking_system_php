<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// Handle approving an agent
if (isset($_GET['approve_id'])) {
    $stmt = $conn->prepare("UPDATE users SET is_approved = 1 WHERE id = ? AND role = 'agent'");
    $stmt->bind_param("i", $_GET['approve_id']);
    $stmt->execute();
}
// Fetch pending agents
$pending_agents = $conn->query("SELECT u.id, a.full_name, a.phone_number, u.email FROM users u JOIN agents a ON u.related_id = a.id WHERE u.is_approved = 0 AND u.role = 'agent'");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Pending Agent Approvals</h3>
<table border="1">
    <tr><th>Name</th><th>Phone</th><th>Email</th><th>Action</th></tr>
    <?php while ($agent = $pending_agents->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($agent['full_name']); ?></td>
        <td><?php echo htmlspecialchars($agent['phone_number']); ?></td>
        <td><?php echo htmlspecialchars($agent['email']); ?></td>
        <td><a href="?approve_id=<?php echo $agent['id']; ?>">Approve</a></td>
    </tr>
    <?php endwhile; ?>
</table>