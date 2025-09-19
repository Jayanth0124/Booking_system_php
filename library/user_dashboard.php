<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <h2>User Menu</h2>
    <ul>
        <li><a href="search_books.php">Search for Books</a></li>
        <li><a href="borrow_history.php">My Borrow History</a></li>
        <li><a href="check_fines.php">Check My Fines</a></li>
        </ul>
    <a href="logout.php">Logout</a>
</body>
</html>