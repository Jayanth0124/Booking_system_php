<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') { header("Location: login.php"); exit(); }

if (isset($_POST['submit_complaint'])) {
    $doc_path = null;
    if (isset($_FILES['document']) && $_FILES['document']['size'] > 0) {
        $target_dir = "uploads/";
        $doc_path = $target_dir . time() . '_' . basename($_FILES["document"]["name"]);
        move_uploaded_file($_FILES["document"]["tmp_name"], $doc_path);
    }
    $stmt = $conn->prepare("INSERT INTO complaints (complainant_id, category_id, complaint_title, description, document_path, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssss", $_SESSION['related_id'], $_POST['category_id'], $_POST['title'], $_POST['description'], $doc_path, $_POST['latitude'], $_POST['longitude']);
    $stmt->execute();
    header("Location: my_complaints.php");
    exit();
}
$categories = $conn->query("SELECT * FROM complaint_categories");
?>
<a href="user_dashboard.php">Back to Dashboard</a>
<h3>Post a New Complaint</h3>
<form method="post" enctype="multipart/form-data">
    Complaint Title: <input type="text" name="title" required><br>
    Category: <select name="category_id" required>
        <?php while ($cat = $categories->fetch_assoc()): ?>
        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
        <?php endwhile; ?>
    </select><br>
    Description: <textarea name="description" rows="5" required></textarea><br>
    Location Latitude: <input type="text" name="latitude"><br>
    Location Longitude: <input type="text" name="longitude"><br>
    <em><small>On Google Maps, right-click a location to get its latitude and longitude.</small></em><br>
    Supporting Document (Optional): <input type="file" name="document"><br>
    <button type="submit" name="submit_complaint">Submit Complaint</button>
</form>