<?php
include 'db_connect.php';

// Ensure any user is logged in to view this page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Create a dynamic link back to the user's correct dashboard.
$dashboard_link = $_SESSION['role'] . "_dashboard.php"; // e.g., user_dashboard.php
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Admin</title>
</head>
<body>
    <a href="<?php echo $dashboard_link; ?>">← Back to Dashboard</a>
    <h2>Contact Administrator</h2>

    <div style="border: 1px solid #ccc; padding: 15px;">
        <p>If you need assistance or have questions about the system, please contact the administrator using the details below.</p>
        
        <h4>Contact Information:</h4>
        <ul>
            <li><strong>Email:</strong> <a href="mailto:admin@complaintease.com">admin@complaintease.com</a></li>
            <li><strong>Phone:</strong> +91 98765 43210</li>
            <li><strong>Office Hours:</strong> Monday - Friday, 9:00 AM - 5:00 PM</li>
        </ul>
    </div>
</body>
</html>