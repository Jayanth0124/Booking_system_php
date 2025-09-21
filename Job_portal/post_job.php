<?php
include 'db_connect.php';
// Ensure the user is a logged-in director.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'director') {
    header("Location: login.php");
    exit();
}
$director_id = $_SESSION['related_id'];

// Handle the form submission.
if (isset($_POST['post_job'])) {
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $target_dir = "uploads/";
        $image_path = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }
    
    $stmt = $conn->prepare("INSERT INTO jobs (director_id, category_id, job_title, description, location, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $director_id, $_POST['category_id'], $_POST['job_title'], $_POST['description'], $_POST['location'], $image_path);
    $stmt->execute();
    
    header("Location: my_jobs.php"); // Redirect to the list of their jobs
    exit();
}

// Fetch categories for the dropdown menu.
$categories = $conn->query("SELECT * FROM job_categories ORDER BY category_name");
?>
<!DOCTYPE html>
<html>
<head><title>Post New Vacancy</title></head>
<body>
    <a href="director_dashboard.php">← Back to Dashboard</a>
    <h2>Post a New Job Vacancy</h2>
    <form method="post" enctype="multipart/form-data">
        Job Title:<br>
        <input type="text" name="job_title" required size="50"><br><br>
        
        Job Category:<br>
        <select name="category_id" required>
            <option value="">-- Select a Category --</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
            <?php endwhile; ?>
        </select><br><br>

        Location (e.g., Chennai, Remote):<br>
        <input type="text" name="location" required size="50"><br><br>
        
        Job Description:<br>
        <textarea name="description" required rows="10" cols="50"></textarea><br><br>
        
        Company Logo/Image (Optional):<br>
        <input type="file" name="image"><br><br>
        
        <button type="submit" name="post_job">Post Vacancy</button>
    </form>
</body>
</html>