<?php
include 'db_connect.php';
if (!isset($_SESSION['voter_id_number']) || !isset($_POST['submit_vote'])) { header("Location: login.php"); exit(); }

$voter_id = $_SESSION['voter_db_id'];
$election_id = $_POST['election_id'];
$candidate_id = $_POST['candidate_id'];

$conn->begin_transaction();
try {
    // Check one last time if the voter has already voted
    $voter_check = $conn->query("SELECT has_voted FROM voters WHERE id = $voter_id FOR UPDATE")->fetch_assoc();
    if ($voter_check['has_voted']) {
        throw new Exception("You have already cast your vote.");
    }
    
    // 1. Insert the vote into the votes table
    $stmt_vote = $conn->prepare("INSERT INTO votes (election_id, voter_id, candidate_id) VALUES (?, ?, ?)");
    $stmt_vote->bind_param("iii", $election_id, $voter_id, $candidate_id);
    $stmt_vote->execute();
    
    // 2. Mark the voter as 'has_voted'
    $conn->query("UPDATE voters SET has_voted = 1 WHERE id = $voter_id");
    
    $conn->commit();
    header("Location: voter_dashboard.php");
    exit();
    
} catch (Exception $e) {
    $conn->rollback();
    // Redirect to dashboard with an error, or just show the message
    die("Error: Could not cast vote. It's possible you have already voted. " . $e->getMessage());
}
?>