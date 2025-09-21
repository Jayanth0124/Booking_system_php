<?php
include 'db_connect.php';

// 1. Ensure the user is logged in and is an officer.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'officer') {
    header("Location: login.php");
    exit();
}

$officer_id = $_SESSION['related_id'];

// 2. This block runs when the officer submits the update form.
if (isset($_POST['submit_update'])) {
    $complaint_id = $_POST['complaint_id'];
    $new_status = $_POST['status'];
    $update_text = $_POST['update_text'];
    $proof_path = null;

    // Handle the "proof of work" file upload
    if (isset($_FILES['proof_doc']) && $_FILES['proof_doc']['size'] > 0) {
        $target_dir = "proofs/";
        $proof_path = $target_dir . time() . '_' . basename($_FILES["proof_doc"]["name"]);
        move_uploaded_file($_FILES["proof_doc"]["tmp_name"], $proof_path);
    }

    // Use a transaction for data integrity
    $conn->begin_transaction();
    try {
        // a. Log the action in the 'complaint_updates' table for history
        $stmt_log = $conn->prepare("INSERT INTO complaint_updates (complaint_id, updated_by_officer_id, update_text, proof_path) VALUES (?, ?, ?, ?)");
        $stmt_log->bind_param("iiss", $complaint_id, $officer_id, $update_text, $proof_path);
        $stmt_log->execute();

        // b. Update the main status in the 'complaints' table
        $stmt_update = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ? AND officer_id = ?");
        $stmt_update->bind_param("sii", $new_status, $complaint_id, $officer_id);
        $stmt_update->execute();
        
        $conn->commit();
        header("Location: view_assigned_complaints.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: Could not update complaint. " . $e->getMessage();
    }
}


// 3. This block runs when the page first loads to display the complaint details.
if (!isset($_GET['id'])) {
    echo "No complaint specified.";
    exit();
}
$complaint_id = $_GET['id'];

// Fetch complaint details, ensuring it's assigned to this officer.
$stmt = $conn->prepare("SELECT * FROM complaints WHERE id = ? AND officer_id = ?");
$stmt->bind_param("ii", $complaint_id, $officer_id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();

if (!$complaint) {
    echo "Complaint not found or not assigned to you.";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Complaint</title>
</head>
<body>
    <a href="view_assigned_complaints.php">← Back to Assigned Complaints</a>
    <h2>Update Complaint #<?php echo htmlspecialchars($complaint['id']); ?></h2>

    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
        <h4>Current Complaint Details</h4>
        <p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['complaint_title']); ?></p>
        <p><strong>Status:</strong> <b><?php echo htmlspecialchars($complaint['status']); ?></b></p>
        <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>
    </div>

    <hr>
    <h3>Post an Update</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="complaint_id" value="<?php echo $complaint['id']; ?>">
        
        New Status:<br>
        <select name="status" required>
            <option value="In Progress" <?php if($complaint['status']=='In Progress') echo 'selected'; ?>>In Progress</option>
            <option value="Resolved" <?php if($complaint['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
        </select><br><br>

        Update Notes / Resolution Summary:<br>
        <textarea name="update_text" rows="5" cols="50" required></textarea><br><br>

        Upload Proof of Work (Optional Image/Document):<br>
        <input type="file" name="proof_doc"><br><br>
        
        <button type="submit" name="submit_update">Submit Update</button>
    </form>
</body>
</html>