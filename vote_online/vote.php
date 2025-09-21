<?php
include 'db_connect.php';
if (!isset($_SESSION['voter_id_number'])) { header("Location: login.php"); exit(); }
$voter = $conn->query("SELECT has_voted FROM voters WHERE id = ".$_SESSION['voter_db_id'])->fetch_assoc();
if ($voter['has_voted']) { header("Location: voter_dashboard.php"); exit(); }

$active_election = $conn->query("SELECT * FROM elections WHERE is_active = 1 LIMIT 1")->fetch_assoc();
$candidates = $conn->query("SELECT * FROM candidates WHERE election_id = ".$active_election['id']);
?>
<h2><?php echo htmlspecialchars($active_election['election_title']); ?></h2>
<p>Please select one candidate and click "Submit Vote". This action is final.</p>
<form action="submit_vote.php" method="post">
    <input type="hidden" name="election_id" value="<?php echo $active_election['id']; ?>">
    <?php while ($candidate = $candidates->fetch_assoc()): ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <input type="radio" name="candidate_id" value="<?php echo $candidate['id']; ?>" required>
        <b><?php echo htmlspecialchars($candidate['candidate_name']); ?></b> (<?php echo htmlspecialchars($candidate['party_affiliation']); ?>)
    </div>
    <?php endwhile; ?>
    <button type="submit" name="submit_vote" onclick="return confirm('Are you sure? This action cannot be undone.')">Submit Vote</button>
</form>