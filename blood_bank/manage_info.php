<?php
include 'db_connect.php';

// Ensure the user is logged in and is a hospital.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hospital') {
    header("Location: login.php");
    exit();
}
$hospital_id = $_SESSION['related_id'];

// Handle form submission to update the hospital's details.
if (isset($_POST['update_info'])) {
    $stmt = $conn->prepare("UPDATE hospitals SET hospital_name = ?, address = ?, city = ? WHERE id = ?");
    $stmt->bind_param("sssi", $_POST['hospital_name'], $_POST['address'], $_POST['city'], $hospital_id);
    $stmt->execute();
    echo "<p><strong>Information updated successfully!</strong></p>";
}

// Fetch the hospital's current details to pre-fill the form.
$stmt = $conn->prepare("SELECT * FROM hospitals WHERE id = ?");
$stmt->bind_param("i", $hospital_id);
$stmt->execute();
$hospital = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Hospital Info</title>
</head>
<body>
    <a href="hospital_dashboard.php">← Back to Dashboard</a>
    <h2>Manage Hospital / Blood Bank Information</h2>
    <form method="post">
        Hospital Name:<br>
        <input type="text" name="hospital_name" value="<?php echo htmlspecialchars($hospital['hospital_name']); ?>" required size="50"><br><br>
        Address:<br>
        <textarea name="address" required rows="4" cols="50"><?php echo htmlspecialchars($hospital['address']); ?></textarea><br><br>
        City:<br>
        <input type="text" name="city" value="<?php echo htmlspecialchars($hospital['city']); ?>" required size="50"><br><br>
        <button type="submit" name="update_info">Save Changes</button>
    </form>
</body>
</html>