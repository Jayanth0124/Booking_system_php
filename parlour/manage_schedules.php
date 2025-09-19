<?php
include 'db_connect.php'; if (!isset($_SESSION['role']) || $_SESSION['role'] == 'customer') { header("Location: index.php"); exit(); }
// Logic to add/edit schedules and add lockout times would go here.
// This is a complex page, so for simplicity we will just display current schedules.
$schedules = $conn->query("SELECT s.*, st.staff_name FROM schedules s JOIN staff st ON s.staff_id = st.id ORDER BY s.staff_id, s.day_of_week");
$days = [1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday'];
?>
<h3>Staff Weekly Schedules</h3>
<p>On this page, you would add forms to set the standard working hours for each staff member for each day of the week.</p>
<table border="1">
    <tr><th>Staff</th><th>Day</th><th>Start Time</th><th>End Time</th></tr>
    <?php while ($row = $schedules->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
        <td><?php echo $days[$row['day_of_week']]; ?></td>
        <td><?php echo date('g:i A', strtotime($row['start_time'])); ?></td>
        <td><?php echo date('g:i A', strtotime($row['end_time'])); ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<hr>
<h3>Lockout Unavailable Hours</h3>
<p>Here you would add a form to select a staff member, a specific date and time range (e.g., for a doctor's appointment) to make them unavailable for booking.</p>