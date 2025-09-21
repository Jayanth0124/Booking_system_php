<?php
include 'db_connect.php';
// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle cancelling a reservation
if (isset($_GET['cancel_id'])) {
    $stmt = $conn->prepare("UPDATE reservations SET status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $_GET['cancel_id']);
    $stmt->execute();
    header("Location: view_reservations.php");
    exit();
}

// Fetch all reservations with details from other tables
$reservations = $conn->query("
    SELECT 
        res.id, res.reservation_time, res.num_guests, res.status,
        u.username as customer_name,
        r.name as restaurant_name,
        tt.type_name as table_type
    FROM reservations res
    JOIN users u ON res.user_id = u.id
    JOIN restaurants r ON res.restaurant_id = r.id
    JOIN table_types tt ON res.table_type_id = tt.id
    ORDER BY res.reservation_time DESC
");
?>

<!DOCTYPE html>
<html>
<head><title>View All Reservations</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>All Customer Reservations</h2>

    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Restaurant</th>
                <th>Table Type</th>
                <th>Reservation Time</th>
                <th>Guests</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $reservations->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['restaurant_name']); ?></td>
                <td><?php echo htmlspecialchars($row['table_type']); ?></td>
                <td><?php echo date('d M Y, g:i A', strtotime($row['reservation_time'])); ?></td>
                <td><?php echo $row['num_guests']; ?></td>
                <td><b><?php echo $row['status']; ?></b></td>
                <td>
                    <?php if ($row['status'] == 'Confirmed'): ?>
                        <a href="?cancel_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Cancel</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>