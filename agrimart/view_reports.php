<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

// Fetch statistics
$total_buyers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'public'")->fetch_assoc()['count'];
$total_farmers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'farmer'")->fetch_assoc()['count'];
$approved_farmers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'farmer' AND is_verified = 1")->fetch_assoc()['count'];
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_sales = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'Delivered'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head><title>View Reports</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>Platform Reports</h2>
    
    <h3>User Statistics</h3>
    <ul>
        <li>Total Buyers (Public): <b><?php echo $total_buyers; ?></b></li>
        <li>Total Registered Farmers: <b><?php echo $total_farmers; ?></b></li>
        <li>Total Approved Farmers: <b><?php echo $approved_farmers; ?></b></li>
    </ul>

    <h3>Platform Activity</h3>
    <ul>
        <li>Total Products Listed: <b><?php echo $total_products; ?></b></li>
        <li>Total Orders Placed: <b><?php echo $total_orders; ?></b></li>
        <li>Total Sales from Delivered Orders: <b>₹<?php echo number_format($total_sales ?? 0, 2); ?></b></li>
    </ul>
</body>
</html>