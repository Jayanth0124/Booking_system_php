<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') { header("Location: login.php"); exit(); }
?>
<h1>Agent Dashboard</h1>
<p>Welcome, Agent!</p>
<ul>
    <li><a href="manage_customers.php">Create & Manage Customers</a></li>
    <li><a href="agent_manage_policies.php">Manage Policies for Customers</a></li>
    <li><a href="send_reminder.php">Send SMS Alert (Reminder)</a></li>
</ul>
<a href="logout.php">Logout</a>