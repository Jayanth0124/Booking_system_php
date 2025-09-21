<?php
include 'db_connect.php';

// 1. Ensure a user is logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if a complaint ID was provided in the URL.
if (!isset($_GET['id'])) {
    echo "No complaint specified.";
    exit();
}

$complaint_id = $_GET['id'];
$complainant_id = $_SESSION['related_id']; // The ID from the 'complainants' table

// 3. Fetch the main complaint details.
// This query securely checks that the complaint ID belongs to the logged-in user.
$stmt = $conn->prepare("
    SELECT c.*, cat.category_name, o.full_name as officer_name
    FROM complaints c
    JOIN complaint_categories cat ON c.category_id = cat.id
    LEFT JOIN officers o ON c.officer_id = o.id
    WHERE c.id = ? AND c.complainant_id = ?
");
$stmt->bind_param("ii", $complaint_id, $complainant_id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();

if (!$complaint) {
    echo "Complaint not found or you do not have permission to view it.";
    exit();
}

// 4. Fetch all updates/history for this complaint.
$updates_stmt = $conn->prepare("
    SELECT cu.*, o.full_name as officer_name
    FROM complaint_updates cu
    JOIN officers o ON cu.updated_by_officer_id = o.id
    WHERE cu.complaint_id = ?
    ORDER BY cu.update_date ASC
");
$updates_stmt->bind_param("i", $complaint_id);
$updates_stmt->execute();
$updates = $updates_stmt->get_result();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Complaint Details</title>
</head>
<body>
    <a href="my_complaints.php">← Back to My Complaints</a>
    <h2>Complaint Details (ID: #<?php echo htmlspecialchars($complaint['id']); ?>)</h2>

    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
        <h4>Complaint Information</h4>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['complaint_title']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($complaint['category_name']); ?></p>
        <p><strong>Submitted On:</strong> <?php echo date('d M Y, g:i A', strtotime($complaint['submitted_at'])); ?></p>
        <p><strong>Status:</strong> <b><?php echo htmlspecialchars($complaint['status']); ?></b></p>
        <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>
        <p><strong>Assigned Officer:</strong> <?php echo $complaint['officer_name'] ? htmlspecialchars($complaint['officer_name']) : 'Pending Assignment'; ?></p>

        <?php if ($complaint['document_path']): ?>
            <p><strong>Supporting Document:</strong> <a href="<?php echo htmlspecialchars($complaint['document_path']); ?>" target="_blank">View Document</a></p>
        <?php endif; ?>

        <?php if ($complaint['latitude'] && $complaint['longitude']): ?>
            <p><strong>Location:</strong> <a href="https://www.google.com/maps?q=<?php echo $complaint['latitude']; ?>,<?php echo $complaint['longitude']; ?>" target="_blank">View on Google Maps</a></p>
        <?php endif; ?>
    </div>

    <h3>Resolution History & Updates</h3>
    <?php if ($updates->num_rows > 0): ?>
        <?php while ($update = $updates->fetch_assoc()): ?>
            <div style="border: 1px solid #007bff; padding: 10px; margin-bottom: 10px;">
                <p>
                    <strong>Update on <?php echo date('d M Y, g:i A', strtotime($update['update_date'])); ?></strong>
                    by Officer <?php echo htmlspecialchars($update['officer_name']); ?>
                </p>
                <hr>
                <p><?php echo nl2br(htmlspecialchars($update['update_text'])); ?></p>
                <?php if ($update['proof_path']): ?>
                    <p><strong>Proof of Work:</strong> <a href="<?php echo htmlspecialchars($update['proof_path']); ?>" target="_blank">View Proof</a></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No updates have been posted by an officer yet.</p>
    <?php endif; ?>

    <?php if ($complaint['status'] == 'Resolved'): ?>
        <hr>
        <h4>Feedback</h4>
        <p>This complaint has been marked as resolved. Please provide your feedback on the resolution.</p>
        <a href="leave_feedback.php?complaint_id=<?php echo $complaint['id']; ?>">Leave Feedback</a>
    <?php endif; ?>

</body>
</html>