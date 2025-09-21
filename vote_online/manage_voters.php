<?php
include 'db_connect.php';
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }
if (isset($_POST['add_voter'])) {
    $stmt = $conn->prepare("INSERT INTO voters (voter_id_number, password, full_name, ward_number) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $_POST['voter_id'], $_POST['password'], $_POST['full_name'], $_POST['ward']);
    $stmt->execute();
}
$voters = $conn->query("SELECT * FROM voters");
?>
<a href="admin_dashboard.php">Back to Dashboard</a>
<h3>Create New Voter</h3>
<form method="post">
    Voter ID Number: <input type="text" name="voter_id" required><br>
    Full Name: <input type="text" name="full_name" required><br>
    Default Password: <input type="text" name="password" required><br>
    Ward Number: <input type="text" name="ward"><br>
    <button type="submit" name="add_voter">Add Voter</button>
</form>
<hr>
<h3>Voter List</h3>
<table border="1">
    <tr><th>Voter ID</th><th>Name</th><th>Ward</th><th>Has Voted?</th></tr>
    <?php while ($row = $voters->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['voter_id_number']); ?></td>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo htmlspecialchars($row['ward_number']); ?></td>
        <td><?php echo $row['has_voted'] ? 'Yes' : 'No'; ?></td>
    </tr>
    <?php endwhile; ?>
</table>