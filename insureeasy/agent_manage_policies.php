<?php
include 'db_connect.php';

// 1. Ensure the user is logged in and is an agent.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
    header("Location: login.php");
    exit();
}

$agent_id = $_SESSION['related_id'];

// 2. Handle the form submission to create a new policy.
if (isset($_POST['create_policy'])) {
    $customer_id = $_POST['customer_id'];
    $policy_type_id = $_POST['policy_type_id'];
    $policy_number = $_POST['policy_number'];
    $premium_amount = $_POST['premium_amount'];
    $sum_assured = $_POST['sum_assured'];
    $start_date = $_POST['start_date'];
    $next_due_date = $_POST['next_due_date'];
    
    // Security check: Ensure the selected customer belongs to this agent
    $customer_check = $conn->prepare("SELECT id FROM customers WHERE id = ? AND created_by_agent_id = ?");
    $customer_check->bind_param("ii", $customer_id, $agent_id);
    $customer_check->execute();
    $result = $customer_check->get_result();

    if ($result->num_rows > 0) {
        // If customer is valid, insert the new policy
        $stmt = $conn->prepare("INSERT INTO policies (customer_id, policy_type_id, policy_number, premium_amount, sum_assured, start_date, next_due_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisddss", $customer_id, $policy_type_id, $policy_number, $premium_amount, $sum_assured, $start_date, $next_due_date);
        $stmt->execute();
        header("Location: agent_manage_policies.php"); // Redirect to refresh the list
        exit();
    } else {
        echo "Error: Invalid customer selected.";
    }
}

// 3. Fetch data needed for the form and the list display.
// Fetch ONLY the customers created by this agent.
$customers = $conn->query("SELECT id, full_name FROM customers WHERE created_by_agent_id = $agent_id ORDER BY full_name");

// Fetch all available policy types.
$policy_types = $conn->query("SELECT * FROM policy_types ORDER BY type_name");

// Fetch all policies managed by this agent.
$policies = $conn->query("
    SELECT p.*, c.full_name as customer_name, pt.type_name
    FROM policies p
    JOIN customers c ON p.customer_id = c.id
    JOIN policy_types pt ON p.policy_type_id = pt.id
    WHERE c.created_by_agent_id = $agent_id
    ORDER BY p.next_due_date ASC
");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Policies</title>
</head>
<body>
    <a href="agent_dashboard.php">← Back to Dashboard</a>
    <h2>Manage Customer Policies</h2>

    <hr>
    <h3>Create New Policy</h3>
    <form method="post">
        Customer:
        <select name="customer_id" required>
            <option value="">-- Select a Customer --</option>
            <?php while ($customer = $customers->fetch_assoc()): ?>
            <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['full_name']); ?></option>
            <?php endwhile; ?>
        </select><br><br>

        Policy Type:
        <select name="policy_type_id" required>
            <option value="">-- Select Policy Type --</option>
            <?php while ($type = $policy_types->fetch_assoc()): ?>
            <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['type_name']); ?></option>
            <?php endwhile; ?>
        </select><br><br>

        Policy Number: <input type="text" name="policy_number" required><br><br>
        Premium Amount (₹): <input type="text" name="premium_amount" required><br><br>
        Sum Assured (₹): <input type="text" name="sum_assured" required><br><br>
        Start Date: <input type="date" name="start_date" required><br><br>
        Next Due Date: <input type="date" name="next_due_date" required><br><br>

        <button type="submit" name="create_policy">Create Policy</button>
    </form>
    <hr>

    <h3>All Managed Policies</h3>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Policy #</th>
                <th>Customer Name</th>
                <th>Policy Type</th>
                <th>Premium</th>
                <th>Next Due Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($policy = $policies->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($policy['policy_number']); ?></td>
                <td><?php echo htmlspecialchars($policy['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($policy['type_name']); ?></td>
                <td>₹<?php echo number_format($policy['premium_amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($policy['next_due_date']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>