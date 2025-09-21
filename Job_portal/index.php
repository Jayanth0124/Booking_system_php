<?php
include 'db_connect.php';
// Build WHERE clause for filtering
$where_clauses = [];
$params = [];
$types = '';
if (!empty($_GET['category'])) {
    $where_clauses[] = "j.category_id = ?";
    $params[] = $_GET['category']; $types .= 'i';
}
if (!empty($_GET['location'])) {
    $where_clauses[] = "j.location LIKE ?";
    $params[] = "%".$_GET['location']."%"; $types .= 's';
}
// CORRECTED QUERY: Changed 'c.company_name' to 'd.company_name'
$sql = "SELECT j.*, d.company_name FROM jobs j JOIN directors d ON j.director_id = d.id JOIN users u ON d.id=u.related_id AND u.role='director' WHERE u.is_approved=1";
if (!empty($where_clauses)) {
    $sql .= " AND " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY j.post_date DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$jobs = $stmt->get_result();
$categories = $conn->query("SELECT * FROM job_categories");
?>
<!DOCTYPE html>
<html>
<head><title>Job Portal</title></head>
<body>
    <h1>Job Portal</h1>
    <?php if (isset($_SESSION['user_id'])): 
        $role = $_SESSION['role'];
        $dashboard = $role . "_dashboard.php"; // e.g., user_dashboard.php
    ?>
        <p>
            Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>! | 
            <a href="<?php echo $dashboard; ?>">My Dashboard</a> | 
            <a href="logout.php">Logout</a>
        </p>
    <?php else: ?>
        <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
    <?php endif; ?>
    <hr>
    <h3>Filter Jobs</h3>
    <form method="get">
        Category: 
        <select name="category">
            <option value="">All Categories</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?php echo $cat['id']; ?>" <?php if(isset($_GET['category']) && $_GET['category'] == $cat['id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($cat['category_name']); ?>
            </option>
            <?php endwhile; ?>
        </select>
        Location: 
        <input type="text" name="location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
        <button type="submit">Filter</button>
    </form>
    <hr>
    <h3>Available Vacancies</h3>
    <?php if ($jobs->num_rows > 0): ?>
        <?php while ($job = $jobs->fetch_assoc()): ?>
            <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                <h4><?php echo htmlspecialchars($job['job_title']); ?></h4>
                <p>
                    <strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?> | 
                    <strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?>
                </p>
                <a href="job_details.php?id=<?php echo $job['id']; ?>">View Details & Apply</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No jobs found matching your criteria.</p>
    <?php endif; ?>
</body>
</html>