<?php include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') { header("Location: login.php"); exit(); } ?>
<h1>Farmer Dashboard</h1>
<ul>
    <li><a href="manage_products.php">Add/Manage My Products</a></li>
    <li><a href="farmer_manage_orders.php">Manage Customer Orders</a></li>
    <li><a href="my_sales.php">My Sales Report</a></li>
</ul>
<a href="logout.php">Logout</a>