<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hospital') { header("Location: login.php"); exit(); }
$hospital = $conn->query("SELECT * FROM hospitals WHERE id = ".$_SESSION['related_id'])->fetch_assoc();
?>
<h1>Hospital Dashboard</h1>
<p>Welcome, <?php echo htmlspecialchars($hospital['hospital_name']); ?>!</p>
<ul>
    <li><a href="update_stock.php">Update Blood Group Status</a></li>
    <li><a href="manage_info.php">Update Hospital Info</a></li>
</ul>
<a href="logout.php">Logout</a>