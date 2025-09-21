<?php
include 'db_connect.php';
$restaurants = $conn->query("SELECT * FROM restaurants");
?>
<h1>Welcome to KodaikanalEats</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="my_reservations.php">My Reservations</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
<?php endif; ?>
<hr>
<h3>Find a Restaurant</h3>
<hr>
<h3>Our Restaurants</h3>
<?php while ($rest = $restaurants->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
        <h4><?php echo htmlspecialchars($rest['name']); ?></h4>
        <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($rest['cuisine_type']); ?> | <strong>Location:</strong> <?php echo htmlspecialchars($rest['location_area']); ?></p>
        <a href="restaurant_details.php?id=<?php echo $rest['id']; ?>">View Details & Book a Table</a>
    </div>
<?php endwhile; ?>