<?php
include 'db_connect.php';
if (!isset($_GET['service_id'])) { header("Location: index.php"); exit(); }

$service_id = $_GET['service_id'];
$staff_members = $conn->query("SELECT * FROM staff");

// This is a simplified slot display. A real app would use AJAX and more complex logic.
if (isset($_GET['date']) && isset($_GET['staff_id'])) {
    // In a real app, you would generate available slots here based on logic from the thought process.
    // For simplicity, we are showing predefined slots.
    $available_slots = ['09:00:00', '10:30:00', '12:00:00', '14:00:00', '15:30:00'];
}
?>
<h3>Book an Appointment</h3>
<form method="get">
    <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
    Select Staff: 
    <select name="staff_id" required>
        <?php while ($staff = $staff_members->fetch_assoc()): ?>
            <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['staff_name']); ?></option>
        <?php endwhile; ?>
    </select><br>
    Select Date: <input type="date" name="date" required><br>
    <button type="submit">Check Availability</button>
</form>

<?php if (isset($available_slots)): ?>
    <hr><h4>Available Slots for <?php echo htmlspecialchars($_GET['date']); ?>:</h4>
    <?php foreach ($available_slots as $slot): ?>
        <a href="confirm_booking.php?service_id=<?php echo $service_id; ?>&staff_id=<?php echo $_GET['staff_id']; ?>&time=<?php echo $_GET['date'] . ' ' . $slot; ?>">
            <?php echo date('g:i A', strtotime($slot)); ?>
        </a> |
    <?php endforeach; ?>
<?php endif; ?>