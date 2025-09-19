<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch all feedback, joining with users table to get username
$result = $conn->query("SELECT f.*, u.username FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.submitted_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Customer Feedback</title>
    <style>
        .feedback-item { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
        .feedback-item p { margin: 0; }
    </style>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h1>Customer Feedback</h1>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="feedback-item">
                <p>
                    <strong>User:</strong> <?php echo htmlspecialchars($row['username']); ?> | 
                    <strong>Rating:</strong> <?php echo str_repeat('⭐', $row['rating']); ?> | 
                    <strong>Date:</strong> <?php echo $row['submitted_at']; ?>
                </p>
                <hr>
                <p><?php echo nl2br(htmlspecialchars($row['comment_text'])); ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No feedback has been submitted yet.</p>
    <?php endif; ?>

</body>
</html>