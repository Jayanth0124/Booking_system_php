<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <h2>Admin Menu</h2>
    <ul>
        <li><a href="manage_books.php">Manage Books (Add/Update/Delete)</a></li>
        <li><a href="issue_book.php">Issue Book to Student</a></li>
        <li><a href="manage_issued_books.php">View / Return Issued Books</a></li>
        <li><a href="manage_materials.php">Manage Study Materials</a></li>
    </ul>
    <a href="logout.php">Logout</a>
</body>
</html>