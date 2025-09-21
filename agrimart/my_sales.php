<?php
include 'db_connect.php';

// 1. Security Check: Ensure the user is a logged-in farmer.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit();
}

// 2. Get the unique Farmer ID from the logged-in User ID.
$user_id = $_SESSION['user_id'];
$farmer_result = $conn->query("SELECT id FROM farmers WHERE user_id = $user_id");
if ($farmer_result->num_rows == 0) {
    die("Error: Farmer profile not found for this user.");
}
$farmer_id = $farmer_result->fetch_assoc()['id'];

// 3. Fetch all completed sales items for this farmer's products.
// This query joins multiple tables to find all items in 'Delivered' orders
// that were sold by the currently logged-in farmer.
$sales_result = $conn->query("
    SELECT 
        p.name as product_name,
        oi.quantity,
        oi.price_per_item,
        o.order_date,
        o.id as order_id
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE p.farmer_id = $farmer_id AND o.status = 'Delivered'
    ORDER BY o.order_date DESC
");

?>
<!DOCTYPE html>
<html>
<head>
    <title>My Sales Report</title>
</head>
<body>
    <a href="farmer_dashboard.php">← Back to Dashboard</a>
    <h2>My Sales Report</h2>
    <p>This report shows a summary of all your items from completed and delivered orders.</p>

    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Product Name</th>
                <th>Quantity Sold</th>
                <th>Price Per Item</th>
                <th>Item Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grand_total_sales = 0;
            if ($sales_result->num_rows > 0):
                while ($sale = $sales_result->fetch_assoc()):
                    $item_total = $sale['quantity'] * $sale['price_per_item'];
                    $grand_total_sales += $item_total;
            ?>
                <tr>
                    <td>#<?php echo htmlspecialchars($sale['order_id']); ?></td>
                    <td><?php echo date('d M Y', strtotime($sale['order_date'])); ?></td>
                    <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($sale['quantity']); ?></td>
                    <td>₹<?php echo number_format($sale['price_per_item'], 2); ?></td>
                    <td>₹<?php echo number_format($item_total, 2); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">You have no completed sales yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right;"><strong>Grand Total Sales:</strong></td>
                <td><strong>₹<?php echo number_format($grand_total_sales, 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>