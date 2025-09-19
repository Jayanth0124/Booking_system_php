<?php include 'db_connect.php'; ?>
<h1>Online Bus Ticket Booking</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="my_bookings.php">My Bookings</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
<?php endif; ?>
<hr>
<h3>Search for Buses</h3>
<form action="search_results.php" method="get">
    From: <input type="text" name="origin" required><br>
    To: <input type="text" name="destination" required><br>
    Date: <input type="date" name="travel_date" required><br>
    <button type="submit">Search Buses</button>
</form>
