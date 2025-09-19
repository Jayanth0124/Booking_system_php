<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit();
}
?>
<h1>Admin Dashboard</h1>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
<ul>
    <li><a href="manage_rooms.php">Manage Rooms</a></li>
    <li><a href="manage_bookings.php">Manage Bookings</a></li>
    <li><a href="manage_pages.php">Manage Site Pages</a></li>
</ul>
<a href="logout.php">Logout</a>