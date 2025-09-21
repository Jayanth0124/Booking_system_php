<?php
include 'db_connect.php';
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }
$active_election = $conn->query("SELECT * FROM elections WHERE is_active = 1 LIMIT 1")->fetch_assoc();
$results = $conn->query("SELECT c.candidate_name, c.party_affiliation, COUNT(v.id) as vote_count FROM candidates c LEFT JOIN votes v ON c.id = v.candidate_id WHERE c.election_id = ".$active_election['id']." GROUP BY c.id ORDER BY vote_count DESC");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h2>Live Results for: <?php echo htmlspecialchars($active_election['election_title']); ?></h2>
<table border="1">
    <tr><th>Candidate</th><th>Party</th><th>Vote Count</th></tr>
    <?php while ($row = $results->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
        <td><?php echo htmlspecialchars($row['party_affiliation']); ?></td>
        <td><b><?php echo $row['vote_count']; ?></b></td>
    </tr>
    <?php endwhile; ?>
</table>