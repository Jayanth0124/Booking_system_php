<?php include 'db_connect.php'; ?>
<h1>Online Flight Reservation</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="my_tickets.php">My Tickets</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
<?php endif; ?>
<hr>
<h3>Search for Flights</h3>
<form action="search_flights.php" method="get">
    From: <input type="text" name="origin" required><br>
    To: <input type="text" name="destination" required><br>
    Date: <input type="date" name="flight_date" required><br>
    <button type="submit">Search Flights</button>
</form>
<hr>
<h3>View Booking Status</h3>
<form action="booking_status.php" method="get">
    Enter Booking Reference: <input type="text" name="booking_ref" required>
    <button type="submit">Check Status</button>
</form>