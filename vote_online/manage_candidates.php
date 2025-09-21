<?php
include 'db_connect.php';
// Ensure the user is logged in and is an administrator.
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle adding a new candidate
if (isset($_POST['add_candidate'])) {
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['size'] > 0) {
        $target_dir = "uploads/";
        $photo_path = $target_dir . time() . '_' . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_path);
    }
    
    $stmt = $conn->prepare("INSERT INTO candidates (election_id, candidate_name, party_affiliation, photo_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $_POST['election_id'], $_POST['candidate_name'], $_POST['party_affiliation'], $photo_path);
    $stmt->execute();
    header("Location: manage_candidates.php");
    exit();
}

// Handle deleting a candidate
if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    // First, delete the photo file from the server
    $stmt_get = $conn->prepare("SELECT photo_path FROM candidates WHERE id = ?");
    $stmt_get->bind_param("i", $id_to_delete);
    $stmt_get->execute();
    if ($candidate = $stmt_get->get_result()->fetch_assoc()) {
        if (!empty($candidate['photo_path']) && file_exists($candidate['photo_path'])) {
            unlink($candidate['photo_path']);
        }
    }
    // Then, delete the record from the database
    $stmt_del = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt_del->bind_param("i", $id_to_delete);
    $stmt_del->execute();
    header("Location: manage_candidates.php");
    exit();
}

// Fetch data for display
$elections = $conn->query("SELECT * FROM elections WHERE is_active = 1");
$candidates = $conn->query("SELECT c.*, e.election_title FROM candidates c JOIN elections e ON c.election_id = e.id ORDER BY e.election_title, c.candidate_name");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Candidates</title></head>
<body>
    <a href="admin_dashboard.php">← Back to Dashboard</a>
    <h2>Create Election Candidate</h2>

    <form method="post" enctype="multipart/form-data">
        Election:
        <select name="election_id" required>
            <option value="">-- Select Election --</option>
            <?php while ($election = $elections->fetch_assoc()): ?>
            <option value="<?php echo $election['id']; ?>"><?php echo htmlspecialchars($election['election_title']); ?></option>
            <?php endwhile; ?>
        </select><br><br>

        Candidate Name: <input type="text" name="candidate_name" required><br><br>
        Party Affiliation: <input type="text" name="party_affiliation" required><br><br>
        Candidate Photo (Optional): <input type="file" name="photo"><br><br>

        <button type="submit" name="add_candidate">Add Candidate</button>
    </form>
    <hr>

    <h3>Current Candidates</h3>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Party</th>
                <th>Election</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $candidates->fetch_assoc()): ?>
            <tr>
                <td><img src="<?php echo htmlspecialchars($row['photo_path']); ?>" width="50"></td>
                <td><?php echo htmlspecialchars($row['candidate_name']); ?></td>
                <td><?php echo htmlspecialchars($row['party_affiliation']); ?></td>
                <td><?php echo htmlspecialchars($row['election_title']); ?></td>
                <td>
                    <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>