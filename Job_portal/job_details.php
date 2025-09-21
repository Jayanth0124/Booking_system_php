<?php
include 'db_connect.php';
if (!isset($_GET['id'])) { header("Location: index.php"); exit(); }
$job_id = $_GET['id'];
$stmt = $conn->prepare("SELECT j.*, d.company_name FROM jobs j JOIN directors d ON j.director_id = d.id WHERE j.id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();
?>
<h1><?php echo htmlspecialchars($job['job_title']); ?></h1>
<p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
<p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
<p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
<a href="apply_job.php?job_id=<?php echo $job['id']; ?>" style="font-size: 1.2em; padding: 10px; background-color: #28a745; color: white;">Apply for this Vacancy</a>