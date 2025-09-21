<?php
include 'db_connect.php';
if (!isset($_SESSION['voter_id_number'])) { header("Location: login.php"); exit(); }
$voter = $conn->query("SELECT * FROM voters WHERE id = ".$_SESSION['voter_db_id'])->fetch_assoc();
?>
<h1>Voter Dashboard</h1>
<p>Welcome, <?php echo htmlspecialchars($voter['full_name']); ?> (Voter ID: <?php echo htmlspecialchars($voter['voter_id_number']); ?>)</p>
<hr>
<?php if ($voter['has_voted']): ?>
    <p style="color:green; font-weight:bold;">Thank you for voting! Your vote has been recorded.</p>
<?php else: ?>
    <a href="vote.php" style="font-size:1.5em; padding:15px; background-color:blue; color:white;">Proceed to Vote</a>
<?php endif; ?>
<hr>
<a href="logout.php">Logout</a>