<?php
include 'db_connect.php';
if (!isset($_GET['dest_id'])) { header("Location: index.php"); exit(); }
$dest_id = $_GET['dest_id'];
$hotels = $conn->query("SELECT * FROM hotels WHERE destination_id = $dest_id");
?>
<h3>Hotels & Hostels</h3>
<?php while ($hotel = $hotels->fetch_assoc()): ?>
    <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
        <h4><?php echo htmlspecialchars($hotel['hotel_name']); ?></h4>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($hotel['address']); ?></p>
        <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($hotel['price_per_night']); ?> / night</p>
        <a href="book_hotel.php?hotel_id=<?php echo $hotel['id']; ?>">Book Now</a>
    </div>
<?php endwhile; ?>