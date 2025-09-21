<?php
include 'db_connect.php';

// Check if search parameters are provided.
if (!isset($_GET['city']) || !isset($_GET['blood_group'])) {
    header("Location: index.php");
    exit();
}
$city = $_GET['city'];
$blood_group = $_GET['blood_group'];

// Fetch donors matching the search criteria.
$stmt = $conn->prepare("SELECT full_name, blood_group, city, phone_number FROM donors WHERE city = ? AND blood_group = ?");
$stmt->bind_param("ss", $city, $blood_group);
$stmt->execute();
$donors = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Donor Search Results</title>
</head>
<body>
    <a href="index.php">← Back to Search</a>
    <h2>Search Results for Donors with Blood Group '<?php echo htmlspecialchars($blood_group); ?>' in '<?php echo htmlspecialchars($city); ?>'</h2>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Blood Group</th>
                <th>City</th>
                <th>Contact Number</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($donors->num_rows > 0): ?>
                <?php while ($donor = $donors->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($donor['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($donor['blood_group']); ?></td>
                    <td><?php echo htmlspecialchars($donor['city']); ?></td>
                    <td><?php echo htmlspecialchars($donor['phone_number']); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No donors found matching your criteria.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>