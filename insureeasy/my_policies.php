<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') { header("Location: login.php"); exit(); }
$customer_id = $_SESSION['related_id'];
$policies = $conn->query("SELECT p.*, pt.type_name FROM policies p JOIN policy_types pt ON p.policy_type_id = pt.id WHERE p.customer_id = $customer_id");
?>
<a href="customer_dashboard.php">Back to Dashboard</a>
<h3>My Policies</h3>
<table border="1">
    <tr><th>Policy #</th><th>Type</th><th>Premium</th><th>Sum Assured</th><th>Start Date</th><th>Next Due Date</th></tr>
    <?php while ($row = $policies->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['policy_number']); ?></td>
        <td><?php echo htmlspecialchars($row['type_name']); ?></td>
        <td>₹<?php echo $row['premium_amount']; ?></td>
        <td>₹<?php echo $row['sum_assured']; ?></td>
        <td><?php echo $row['start_date']; ?></td>
        <td><?php echo $row['next_due_date']; ?></td>
    </tr>
    <?php endwhile; ?>
</table>