<?php
include 'db_connect.php';
if (!isset($_GET['slug'])) {
    header("Location: index.php"); exit();
}

$slug = $_GET['slug'];
$stmt = $conn->prepare("SELECT page_title, page_content FROM pages WHERE page_slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
if ($page = $result->fetch_assoc()) {
    // Content is displayed here
} else {
    echo "Page not found.";
    exit();
}
?>
<a href="index.php">Back to Home</a>
<hr>
<h1><?php echo htmlspecialchars($page['page_title']); ?></h1>
<div>
    <?php echo nl2br(htmlspecialchars($page['page_content'])); ?>
</div>