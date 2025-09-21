<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') { header("Location: login.php"); exit(); }
$agent_id = $_SESSION['related_id'];
// Handle Creating a new customer
if (isset($_POST['create_customer'])) {
    $conn->begin_transaction();
    try {
        $stmt_cust = $conn->prepare("INSERT INTO customers (full_name, phone_number, address, created_by_agent_id) VALUES (?, ?, ?, ?)");
        $stmt_cust->bind_param("sssi", $_POST['full_name'], $_POST['phone_number'], $_POST['address'], $agent_id);
        $stmt_cust->execute();
        $customer_id = $conn->insert_id;
        // Automatically create a login for the new customer
        $stmt_user = $conn->prepare("INSERT INTO users (email, password, role, related_id, is_approved) VALUES (?, ?, 'customer', ?, 1)");
        $stmt_user->bind_param("ssi", $_POST['email'], $_POST['password'], $customer_id);
        $stmt_user->execute();
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to create customer. Email may already be in use.";
    }
}
$customers = $conn->query("SELECT * FROM customers WHERE created_by_agent_id = $agent_id");
?>
<a href="agent_dashboard.php">Back to Dashboard</a>
<h3>Create New Customer</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Phone Number: <input type="text" name="phone_number" required><br>
    Address: <textarea name="address" required></textarea><br>
    Customer Login Email: <input type="email" name="email" required><br>
    Customer Password: <input type="text" name="password" required><br>
    <button type="submit" name="create_customer">Create Customer</button>
</form>
<hr>
<h3>Your Customers</h3>
<table border="1">
    <tr><th>Name</th><th>Phone</th><th>Address</th></tr>
    <?php while ($row = $customers->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
        <td><?php echo htmlspecialchars($row['address']); ?></td>
    </tr>
    <?php endwhile; ?>
</table>