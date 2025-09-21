<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }
?>
<h1>Admin Dashboard</h1>
<p>Welcome, Administrator!</p>
<ul>
    <li><a href="approve_agents.php">Approve New Agents</a></li>
    <li><a href="view_agents.php">View All Agent Details</a></li>
</ul>
<a href="logout.php">Logout</a>