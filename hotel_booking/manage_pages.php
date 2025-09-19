<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $page_id = $_POST['page_id'];
    $content = $_POST['page_content'];
    $stmt = $conn->prepare("UPDATE pages SET page_content = ? WHERE id = ?");
    $stmt->bind_param("si", $content, $page_id);
    $stmt->execute();
}

// Fetch all pages
$result = $conn->query("SELECT * FROM pages");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Edit Site Pages</h3>
<?php while ($row = $result->fetch_assoc()): ?>
    <hr>
    <h4><?php echo htmlspecialchars($row['page_title']); ?></h4>
    <form method="post">
        <input type="hidden" name="page_id" value="<?php echo $row['id']; ?>">
        <textarea name="page_content" rows="10" cols="80"><?php echo htmlspecialchars($row['page_content']); ?></textarea><br>
        <button type="submit">Save Changes</button>
    </form>
<?php endwhile; ?>