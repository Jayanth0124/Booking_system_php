<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login.php"); exit(); }

if (isset($_POST['create_officer'])) {
    $conn->begin_transaction();
    try {
        $stmt_off = $conn->prepare("INSERT INTO officers (full_name, department) VALUES (?, ?)");
        $stmt_off->bind_param("ss", $_POST['full_name'], $_POST['department']);
        $stmt_off->execute();
        $officer_id = $conn->insert_id;

        $stmt_user = $conn->prepare("INSERT INTO users (email, password, role, related_id) VALUES (?, ?, 'officer', ?)");
        $stmt_user->bind_param("ssi", $_POST['email'], $_POST['password'], $officer_id);
        $stmt_user->execute();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback(); echo "Failed to create officer.";
    }
}
$officers = $conn->query("SELECT o.*, u.email FROM officers o JOIN users u ON o.id = u.related_id");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Generate ID & Password for Officer</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Department: <input type="text" name="department" required><br>
    Login Email: <input type="email" name="email" required><br>
    Password: <input type="text" name="password" required><br>
    <button type="submit" name="create_officer">Create Officer</button>
</form>
<hr>
<h3>Existing Officers</h3>
<table border="1">
    <tr><th>Name</th><th>Department</th><th>Login Email</th></tr>
    <?php while ($row = $officers->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo htmlspecialchars($row['department']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
    </tr>
    <?php endwhile; ?>
</table>