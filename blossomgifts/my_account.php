<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
?>
<h1>My Account</h1>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
<ul>
    <li><a href="my_orders.php">View My Order History</a></li>
    <li><a href="edit_profile.php">Edit My Profile & Info</a></li>
</ul>
<a href="logout.php">Logout</a>