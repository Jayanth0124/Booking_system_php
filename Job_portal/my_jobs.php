<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'director') { header("Location: login.php"); exit(); }
$director_id = $_SESSION['related_id'];
$jobs = $conn->query("SELECT j.*, (SELECT COUNT(*) FROM applications WHERE job_id = j.id) as app_count FROM jobs j WHERE j.director_id = $director_id");
?>
<a href="director_dashboard.php">Back to Dashboard</a>
<h3>Your Posted Vacancies</h3>
<table border="1">
    <tr><th>Title</th><th>Location</th><th>Applicants</th><th>Action</th></tr>
    <?php while ($job = $jobs->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($job['job_title']); ?></td>
        <td><?php echo htmlspecialchars($job['location']); ?></td>
        <td><?php echo $job['app_count']; ?></td>
        <td><a href="view_applicants.php?job_id=<?php echo $job['id']; ?>">View Applicants</a></td>
    </tr>
    <?php endwhile; ?>
</table>