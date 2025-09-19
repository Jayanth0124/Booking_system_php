<?php include 'db_connect.php'; 
$services = $conn->query("SELECT * FROM services");
?>
<h1>Beauty Parlour Appointments</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="my_appointments.php">My Appointments</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
<?php endif; ?>
<hr>
<h2>Our Services</h2>
<table border="1">
    <tr><th>Service</th><th>Duration</th><th>Price</th><th>Action</th></tr>
    <?php while ($service = $services->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($service['service_name']); ?></td>
        <td><?php echo $service['duration_minutes']; ?> mins</td>
        <td>₹<?php echo $service['price']; ?></td>
        <td><a href="booking.php?service_id=<?php echo $service['id']; ?>">Book Now</a></td>
    </tr>
    <?php endwhile; ?>
</table>