<?php include 'db_connect.php'; $pnr = $_GET['pnr']; ?>
<h1>Booking Confirmed!</h1>
<h2>Your PNR is: <?php echo htmlspecialchars($pnr); ?></h2>
<p>Please save this PNR for future reference. You can check the status from the homepage.</p>
<a href="my_bookings.php">View all my bookings</a>