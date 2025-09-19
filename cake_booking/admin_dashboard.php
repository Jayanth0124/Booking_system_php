<?php include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit(); } ?>
<h1>Admin Dashboard</h1>
<ul>
    <li><a href="manage_products.php">Manage Products (Cakes)</a></li>
    <li><a href="manage_orders.php">Manage Orders</a></li>
</ul>
<a href="logout.php">Logout</a>