<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

// Handle Return Book
if (isset($_GET['return_id'])) {
    $issue_id = $_GET['return_id'];
    $return_date = date('Y-m-d');

    $conn->begin_transaction();
    try {
        // Get issue details
        $stmt = $conn->prepare("SELECT book_id, user_id, due_date FROM issued_books WHERE id = ? AND status = 'issued'");
        $stmt->bind_param("i", $issue_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $issue_details = $result->fetch_assoc();

        if ($issue_details) {
            // 1. Update issued_books status and return date
            $stmt = $conn->prepare("UPDATE issued_books SET return_date = ?, status = 'returned' WHERE id = ?");
            $stmt->bind_param("si", $return_date, $issue_id);
            $stmt->execute();

            // 2. Increase book available quantity
            $stmt = $conn->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?");
            $stmt->bind_param("i", $issue_details['book_id']);
            $stmt->execute();

            // 3. Check for fine
            $due_date = new DateTime($issue_details['due_date']);
            $return_date_obj = new DateTime($return_date);
            if ($return_date_obj > $due_date) {
                $interval = $return_date_obj->diff($due_date);
                $days_late = $interval->days;
                $fine_amount = $days_late * 10; // Fine is 10 per day
                $stmt = $conn->prepare("INSERT INTO fines (user_id, issued_book_id, amount) VALUES (?, ?, ?)");
                $stmt->bind_param("iid", $issue_details['user_id'], $issue_id, $fine_amount);
                $stmt->execute();
            }
            $conn->commit();
            echo "Book returned successfully.";
        } else {
            $conn->rollback();
            echo "Book already returned or invalid ID.";
        }
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        throw $exception;
    }
}

$result = $conn->query("SELECT ib.id, b.title, u.username, ib.issue_date, ib.due_date, ib.status FROM issued_books ib JOIN books b ON ib.book_id = b.id JOIN users u ON ib.user_id = u.id ORDER BY ib.status, ib.issue_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Issued Books</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>Issued Books List</h2>
    <table border="1">
        <tr>
            <th>Issue ID</th>
            <th>Book Title</th>
            <th>User</th>
            <th>Issue Date</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo $row['issue_date']; ?></td>
            <td><?php echo $row['due_date']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <?php if ($row['status'] == 'issued'): ?>
                    <a href="manage_issued_books.php?return_id=<?php echo $row['id']; ?>">Mark as Returned</a>
                <?php else: ?>
                    Returned
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>