<?php
include 'db_connect.php';
$destinations = $conn->query("SELECT * FROM destinations");
?>
<h1>Welcome to Wanderlust Travels</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="my_bookings.php">My Bookings</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
<?php endif; ?>
<hr>
<h3>Explore Our Destinations</h3>
<?php while ($dest = $destinations->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
        <h4><?php echo htmlspecialchars($dest['destination_name']); ?></h4>
        <p><?php echo htmlspecialchars($dest['description']); ?></p>
        <a href="destination_details.php?id=<?php echo $dest['id']; ?>">View Details</a>
    </div>
<?php endwhile; ?>