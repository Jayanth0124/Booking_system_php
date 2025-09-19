<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php"); exit();
}

$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $sql = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_term = "%" . $search_term . "%";
    $stmt->bind_param("ss", $like_term, $like_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM books");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Books</title>
</head>
<body>
    <a href="user_dashboard.php">Back to Dashboard</a>
    <h2>Search Books</h2>
    <form method="get" action="">
        <input type="text" name="search" placeholder="Enter title or author" value="<?php echo htmlspecialchars($search_term); ?>">
        <input type="submit" value="Search">
    </form>
    <hr>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>ISBN</th>
            <th>Available</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['author']); ?></td>
            <td><?php echo htmlspecialchars($row['isbn']); ?></td>
            <td><?php echo $row['available_quantity'] > 0 ? 'Yes' : 'No'; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>