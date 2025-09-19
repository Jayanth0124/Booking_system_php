<?php include 'db_connect.php'; ?>
<h1>Welcome to Our Hotel</h1>

<?php if (isset($_SESSION['user_id'])): ?>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! | <a href="my_bookings.php">My Bookings</a> | <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
<?php endif; ?>

<hr>
<h3>Check Room Availability</h3>
<form action="rooms.php" method="get">
    Check-in Date: <input type="date" name="check_in" required><br>
    Check-out Date: <input type="date" name="check_out" required><br>
    <button type="submit">Check Availability</button>
</form>
<hr>

<h4>Hotel Information</h4>
<ul>
    <li><a href="page.php?slug=about-us">About Us</a></li>
    <li><a href="page.php?slug=contact-us">Contact Us</a></li>
    <li><a href="page.php?slug=faq">Customer Service Q&A</a></li>
    <li><a href="page.php?slug=privacy-policy">Privacy Policy</a></li>
</ul>