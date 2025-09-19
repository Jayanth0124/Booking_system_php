<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];
    $user_id = $_POST['user_id'];
    $due_date = $_POST['due_date'];
    $issue_date = date('Y-m-d');

    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Check if book is available
        $book_sql = "SELECT available_quantity FROM books WHERE id = ? AND available_quantity > 0";
        $stmt = $conn->prepare($book_sql);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $book_result = $stmt->get_result();

        if ($book_result->num_rows > 0) {
            // 2. Decrease available quantity
            $update_sql = "UPDATE books SET available_quantity = available_quantity - 1 WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("i", $book_id);
            $stmt->execute();

            // 3. Insert into issued_books table
            $insert_sql = "INSERT INTO issued_books (book_id, user_id, issue_date, due_date) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iiss", $book_id, $user_id, $issue_date, $due_date);
            $stmt->execute();

            $conn->commit();
            echo "Book issued successfully!";
        } else {
            echo "Book is not available or does not exist.";
        }
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        echo "Error: " . $exception->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Issue Book</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>Issue Book to a Student</h2>
    <form method="post" action="">
        Book ID: <input type="number" name="book_id" required><br><br>
        Student User ID: <input type="number" name="user_id" required><br><br>
        Due Date: <input type="date" name="due_date" required><br><br>
        <input type="submit" value="Issue Book">
    </form>
</body>
</html>