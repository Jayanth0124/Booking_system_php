<?php
include 'db_connect.php';
// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle form submissions for Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_news'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $id = $_POST['id'];
        if (empty($id)) {
            $stmt = $conn->prepare("INSERT INTO news (title, content) VALUES (?, ?)");
            $stmt->bind_param("ss", $title, $content);
        } else {
            $stmt = $conn->prepare("UPDATE news SET title = ?, content = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $content, $id);
        }
        $stmt->execute();
    }
}

// Handle Delete News
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_news.php");
    exit();
}

// Fetch data for pre-filling the edit form
$news_to_edit = ['id' => '', 'title' => '', 'content' => ''];
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $news_to_edit = $result->fetch_assoc();
}

// Fetch all news articles
$result = $conn->query("SELECT * FROM news ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage News</title>
</head>
<body>
    <a href="admin_dashboard.php">Back to Dashboard</a>
    <h2>Add / Edit News Article</h2>
    <form method="post" action="manage_news.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($news_to_edit['id']); ?>">
        Title: <br>
        <input type="text" name="title" size="50" value="<?php echo htmlspecialchars($news_to_edit['title']); ?>" required><br><br>
        Content: <br>
        <textarea name="content" rows="8" cols="50" required><?php echo htmlspecialchars($news_to_edit['content']); ?></textarea><br><br>
        <button type="submit" name="save_news">Save News</button>
    </form>

    <hr>
    <h2>Existing News Articles</h2>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td>
                <a href="manage_news.php?edit_id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="manage_news.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>