<?php include 'db_connect.php'; ?>
<h1>Online Train Reservation</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="my_bookings.php">My Bookings</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
<?php endif; ?>
<hr>
<h3>Search for Trains</h3>
<form action="search_results.php" method="get">
    From: <input type="text" name="origin" required><br>
    To: <input type="text" name="destination" required><br>
    Date of Journey: <input type="date" name="journey_date" required><br>
    <button type="submit">Search Trains</button>
</form>
<hr>
<h3>Check PNR Status</h3>
<form action="pnr_status.php" method="get">
    Enter PNR Number: <input type="text" name="pnr" required>
    <button type="submit">Check Status</button>
</form>