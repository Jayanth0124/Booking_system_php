<?php
include 'db_connect.php';
// Basic search by city
$city = $_GET['city'] ?? '';
$sql = "SELECT * FROM hospitals WHERE is_approved = 1";
if ($city) {
    $sql .= " AND city LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_city = "%$city%";
    $stmt->bind_param("s", $like_city);
} else {
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$hospitals = $stmt->get_result();
?>
<h1>Find a Hospital</h1>
<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'patient'): ?>
    <p>Welcome, Patient! | <a href="patient_dashboard.php">My Dashboard</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> or <a href="register.php">Register</a> to book an appointment.</p>
<?php endif; ?>
<hr>
<form method="get">
    Search by City: <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>">
    <button type="submit">Search</button>
</form>
<hr>
<h3>Available Hospitals</h3>
<?php while ($hospital = $hospitals->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
        <h4><?php echo htmlspecialchars($hospital['hospital_name']); ?></h4>
        <p><?php echo htmlspecialchars($hospital['address']); ?>, <?php echo htmlspecialchars($hospital['city']); ?></p>
        <a href="hospital_details.php?id=<?php echo $hospital['id']; ?>">View Details & Book Appointment</a>
        <?php if ($hospital['latitude'] && $hospital['longitude']): ?>
             | <a href="https://www.google.com/maps?q=<?php echo $hospital['latitude']; ?>,<?php echo $hospital['longitude']; ?>" target="_blank">View on Google Maps</a>
        <?php endif; ?>
    </div>
<?php endwhile; ?>