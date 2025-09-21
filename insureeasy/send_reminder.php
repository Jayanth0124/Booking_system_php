<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') { header("Location: login.php"); exit(); }
$agent_id = $_SESSION['related_id'];
// Simulate sending SMS
if (isset($_POST['send_sms'])) {
    $customer_id = $_POST['customer_id'];
    $policy_number = $_POST['policy_number'];
    $due_date = $_POST['due_date'];
    $message = "Reminder: Your premium for policy #$policy_number is due on $due_date.";
    $stmt = $conn->prepare("INSERT INTO sms_log (customer_id, message, sent_by_agent_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $customer_id, $message, $agent_id);
    $stmt->execute();
    echo "<b>Success: SMS reminder has been logged for the customer.</b>";
}
// Find policies with upcoming dues for this agent's customers
$policies = $conn->query("SELECT p.policy_number, p.next_due_date, c.id as customer_id, c.full_name FROM policies p JOIN customers c ON p.customer_id = c.id WHERE c.created_by_agent_id = $agent_id AND p.next_due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
?>
<a href="agent_dashboard.php">Back to Dashboard</a>
<h3>Send Premium Reminders</h3>
<p>This list shows policies with dues in the next 30 days.</p>
<table border="1">
    <tr><th>Customer</th><th>Policy #</th><th>Due Date</th><th>Action</th></tr>
    <?php while ($row = $policies->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo htmlspecialchars($row['policy_number']); ?></td>
        <td><?php echo $row['next_due_date']; ?></td>
        <td>
            <form method="post" style="margin:0;">
                <input type="hidden" name="customer_id" value="<?php echo $row['customer_id']; ?>">
                <input type="hidden" name="policy_number" value="<?php echo $row['policy_number']; ?>">
                <input type="hidden" name="due_date" value="<?php echo $row['next_due_date']; ?>">
                <button type="submit" name="send_sms">Send SMS (Simulated)</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>