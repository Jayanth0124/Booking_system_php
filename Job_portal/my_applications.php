<?php
include 'db_connect.php';
// Ensure the user is a logged-in job seeker.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}
$applicant_id = $_SESSION['related_id'];

// Fetch all applications for the current user.
$stmt = $conn->prepare("
    SELECT j.job_title, d.company_name, a.application_date, a.status
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN directors d ON j.director_id = d.id
    WHERE a.applicant_id = ?
    ORDER BY a.application_date DESC
");
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$applications = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>My Applications</title></head>
<body>
    <a href="user_dashboard.php">← Back to Dashboard</a>
    <h2>My Application History</h2>

    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>Date Applied</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($applications->num_rows > 0): ?>
                <?php while ($app = $applications->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                    <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                    <td><?php echo date('d M Y, g:i A', strtotime($app['application_date'])); ?></td>
                    <td><b><?php echo htmlspecialchars($app['status']); ?></b></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">You have not applied for any jobs yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>