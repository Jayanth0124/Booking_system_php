<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'public') { header("Location: login.php"); exit(); }
if (isset($_POST['submit_complaint'])) {
    $stmt = $conn->prepare("INSERT INTO complaints (public_user_id, complaint_text) VALUES (?, ?)");
    $stmt->bind_param("is", $_SESSION['related_id'], $_POST['complaint_text']);
    $stmt->execute();
    header("Location: my_complaints.php");
    exit();
}
?>
<a href="public_dashboard.php">Back to Dashboard</a>
<h3>Register a Complaint</h3>
<form method="post">
    Complaint Details:<br>
    <textarea name="complaint_text" rows="5" cols="50" required></textarea><br>
    <button type="submit" name="submit_complaint">Submit</button>
</form>