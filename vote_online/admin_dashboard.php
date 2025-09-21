<?php include 'db_connect.php'; if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); } ?>
<h1>Admin Dashboard</h1>
<ul>
    <li><a href="manage_voters.php">Create & Manage Voter List</a></li>
    <li><a href="manage_candidates.php">Manage Election Candidates</a></li>
    <li><a href="election_results.php">Get Election Results</a></li>
</ul>
<a href="logout.php">Logout</a>