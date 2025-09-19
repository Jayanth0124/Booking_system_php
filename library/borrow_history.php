<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php"); exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT b.title, b.author, ib.issue_date, ib.due_date, ib.return_date, ib.status FROM issued_books ib JOIN books b ON ib.book_id = b.id WHERE ib.user_id = ? ORDER BY ib.issue_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Borrow History</title>
</head>
<body>
    <a href="user_dashboard.php">Back to Dashboard</a>
    <h2>My Borrow History</h2>
    <table border="1">
        <tr>
            <th>Book Title</th>
            <th>Author</th>
            <th>Issue Date</th>
            <th>Due Date (Exp Date)</th>
            <th>Return Date</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['author']); ?></td>
            <td><?php echo $row['issue_date']; ?></td>
            <td><?php echo $row['due_date']; ?></td>
            <td><?php echo $row['return_date'] ? $row['return_date'] : 'Not Returned'; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>