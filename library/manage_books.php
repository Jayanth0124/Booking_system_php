<?php
include 'db_connect.php';
// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

// Handle Add Book
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];
    $stmt = $conn->prepare("INSERT INTO books (title, author, isbn, quantity, available_quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $title, $author, $isbn, $quantity, $quantity);
    $stmt->execute();
}

// Handle Delete Book
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Fetch all books
$result = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Books</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>Add New Book</h2>
    <form method="post" action="">
        Title: <input type="text" name="title" required><br>
        Author: <input type="text" name="author" required><br>
        ISBN: <input type="text" name="isbn" required><br>
        Quantity: <input type="number" name="quantity" required><br>
        <input type="submit" name="add_book" value="Add Book">
    </form>

    <hr>
    <h2>Existing Books</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>ISBN</th>
            <th>Total Quantity</th>
            <th>Available</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['author']); ?></td>
            <td><?php echo htmlspecialchars($row['isbn']); ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo $row['available_quantity']; ?></td>
            <td><a href="manage_books.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>