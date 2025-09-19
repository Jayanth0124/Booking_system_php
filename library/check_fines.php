<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php"); exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT f.amount, f.paid, b.title FROM fines f JOIN issued_books ib ON f.issued_book_id = ib.id JOIN books b ON ib.book_id = b.id WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Fines</title>
</head>
<body>
    <a href="user_dashboard.php">Back to Dashboard</a>
    <h2>My Fines</h2>
    <table border="1">
        <tr>
            <th>Fine Amount</th>
            <th>For Book</th>
            <th>Status</th>
        </tr>
        <?php 
        $total_fine = 0;
        while ($row = $result->fetch_assoc()): 
            if ($row['paid'] == 'no') {
                $total_fine += $row['amount'];
            }
        ?>
        <tr>
            <td>$<?php echo number_format($row['amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo ucfirst($row['paid']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <h3>Total Unpaid Fines: $<?php echo number_format($total_fine, 2); ?></h3>
</body>
</html>