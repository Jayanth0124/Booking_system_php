<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}

$upload_dir = 'uploads/';

// Handle file upload
if (isset($_POST['add_material'])) {
    $title = $_POST['title'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    
    if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
        $filename = basename($_FILES['material_file']['name']);
        $target_path = $upload_dir . $filename;

        // Move the file to the uploads directory
        if (move_uploaded_file($_FILES['material_file']['tmp_name'], $target_path)) {
            $stmt = $conn->prepare("INSERT INTO materials (title, year, semester, file_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siis", $title, $year, $semester, $target_path);
            if ($stmt->execute()) {
                echo "Material uploaded successfully.";
            } else {
                echo "Error inserting into database.";
            }
        } else {
            echo "Error moving the uploaded file.";
        }
    } else {
        echo "Error with file upload. Code: " . $_FILES['material_file']['error'];
    }
}

// Handle delete material
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    // First, get the file path to delete the file from the server
    $stmt = $conn->prepare("SELECT file_path FROM materials WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (file_exists($row['file_path'])) {
            unlink($row['file_path']); // Delete the file
        }
    }

    // Now, delete the record from the database
    $stmt = $conn->prepare("DELETE FROM materials WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_materials.php"); // Redirect to avoid re-deleting on refresh
    exit();
}

$result = $conn->query("SELECT * FROM materials ORDER BY year, semester");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Study Materials</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>Upload New Material</h2>
    <form method="post" action="" enctype="multipart/form-data">
        Title: <input type="text" name="title" required><br><br>
        Year: <input type="number" name="year" min="1" max="4" required><br><br>
        Semester: <input type="number" name="semester" min="1" max="8" required><br><br>
        File: <input type="file" name="material_file" required><br><br>
        <input type="submit" name="add_material" value="Upload Material">
    </form>
    
    <hr>
    <h2>Existing Materials</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Year</th>
            <th>Semester</th>
            <th>File Path</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo $row['year']; ?></td>
            <td><?php echo $row['semester']; ?></td>
            <td><?php echo htmlspecialchars($row['file_path']); ?></td>
            <td><a href="manage_materials.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this?');">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>