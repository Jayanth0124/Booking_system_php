<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'hospital') { header("Location: login.php"); exit(); }

$hospital_id = $_SESSION['related_id'];

// Handle form submission to update details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("UPDATE hospitals SET hospital_name=?, address=?, city=?, latitude=?, longitude=? WHERE id=?");
    $stmt->bind_param("sssssi", $_POST['hospital_name'], $_POST['address'], $_POST['city'], $_POST['latitude'], $_POST['longitude'], $hospital_id);
    $stmt->execute();
    echo "<p><strong>Details updated successfully!</strong></p>";
}

// Fetch current details to pre-fill the form
$stmt = $conn->prepare("SELECT * FROM hospitals WHERE id = ?");
$stmt->bind_param("i", $hospital_id);
$stmt->execute();
$hospital = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head><title>Manage Hospital Details</title></head>
<body>
    <a href="hospital_dashboard.php">Back to Dashboard</a>
    <h2>Update Hospital Details</h2>
    <form method="post">
        Hospital Name: <input type="text" name="hospital_name" value="<?php echo htmlspecialchars($hospital['hospital_name']); ?>" required><br>
        Address: <input type="text" name="address" value="<?php echo htmlspecialchars($hospital['address']); ?>" required><br>
        City: <input type="text" name="city" value="<?php echo htmlspecialchars($hospital['city']); ?>" required><br>
        Latitude: <input type="text" name="latitude" value="<?php echo htmlspecialchars($hospital['latitude']); ?>"><br>
        Longitude: <input type="text" name="longitude" value="<?php echo htmlspecialchars($hospital['longitude']); ?>"><br>
        <em><small>You can get Latitude/Longitude from Google Maps by right-clicking on your location.</small></em><br><br>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>