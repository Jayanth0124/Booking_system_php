<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'donor') { header("Location: login.php"); exit(); }
$donor_id = $_SESSION['related_id'];
// Handle updating donor info
if (isset($_POST['update_info'])) {
    $stmt = $conn->prepare("UPDATE donors SET full_name=?, blood_group=?, phone_number=?, city=?, last_donation_date=? WHERE id=?");
    $stmt->bind_param("sssssi", $_POST['full_name'], $_POST['blood_group'], $_POST['phone_number'], $_POST['city'], $_POST['last_donation_date'], $donor_id);
    $stmt->execute();
    echo "<b>Info updated successfully!</b>";
}
// Fetch current info
$donor = $conn->query("SELECT * FROM donors WHERE id = $donor_id")->fetch_assoc();
?>
<h1>Donor Dashboard</h1>
<p>Welcome, <?php echo htmlspecialchars($donor['full_name']); ?>!</p>
<a href="logout.php">Logout</a>
<hr>
<h3>My Donor Information (Post/Update)</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" value="<?php echo htmlspecialchars($donor['full_name']); ?>" required><br>
    Blood Group: <input type="text" name="blood_group" value="<?php echo htmlspecialchars($donor['blood_group']); ?>" required><br>
    Phone Number: <input type="text" name="phone_number" value="<?php echo htmlspecialchars($donor['phone_number']); ?>" required><br>
    City: <input type="text" name="city" value="<?php echo htmlspecialchars($donor['city']); ?>" required><br>
    Last Donation Date: <input type="date" name="last_donation_date" value="<?php echo htmlspecialchars($donor['last_donation_date']); ?>"><br>
    <button type="submit" name="update_info">Save Changes</button>
</form>