<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') { header("Location: login.php"); exit(); }
$applicant_id = $_SESSION['related_id'];
if (isset($_POST['update_profile'])) {
    $resume_path = $_POST['current_resume'];
    if (isset($_FILES['resume']) && $_FILES['resume']['size'] > 0) {
        $target_dir = "resumes/";
        $resume_path = $target_dir . time() . '_' . basename($_FILES["resume"]["name"]);
        move_uploaded_file($_FILES["resume"]["tmp_name"], $resume_path);
    }
    $stmt = $conn->prepare("UPDATE applicants SET full_name=?, phone_number=?, resume_path=? WHERE id=?");
    $stmt->bind_param("sssi", $_POST['full_name'], $_POST['phone_number'], $resume_path, $applicant_id);
    $stmt->execute();
}
$applicant = $conn->query("SELECT * FROM applicants WHERE id = $applicant_id")->fetch_assoc();
?>
<a href="user_dashboard.php">Back to Dashboard</a>
<h3>My Profile</h3>
<form method="post" enctype="multipart/form-data">
    Full Name: <input type="text" name="full_name" value="<?php echo htmlspecialchars($applicant['full_name']); ?>" required><br>
    Phone Number: <input type="text" name="phone_number" value="<?php echo htmlspecialchars($applicant['phone_number']); ?>" required><br>
    Upload Resume (PDF/DOC): <input type="file" name="resume"><br>
    <input type="hidden" name="current_resume" value="<?php echo htmlspecialchars($applicant['resume_path']); ?>">
    <?php if ($applicant['resume_path']): ?>
        <p>Current Resume: <a href="<?php echo htmlspecialchars($applicant['resume_path']); ?>" target="_blank">View Resume</a></p>
    <?php endif; ?>
    <button type="submit" name="update_profile">Save Profile</button>
</form>