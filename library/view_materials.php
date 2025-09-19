<?php
include 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php"); exit();
}

// Basic search/filter logic
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;

$sql = "SELECT * FROM materials";
$conditions = [];
$params = [];
$types = '';

if ($year > 0) {
    $conditions[] = "year = ?";
    $params[] = $year;
    $types .= 'i';
}
if ($semester > 0) {
    $conditions[] = "semester = ?";
    $params[] = $semester;
    $types .= 'i';
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY year, semester";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html>
<head>
    <title>View Study Materials</title>
</head>
<body>
    <a href="user_dashboard.php">Back to Dashboard</a>
    <h2>Search & Download Materials</h2>
    <form method="get" action="">
        Filter by Year:
        <select name="year">
            <option value="0">All</option>
            <option value="1" <?php if($year==1) echo 'selected'; ?>>1</option>
            <option value="2" <?php if($year==2) echo 'selected'; ?>>2</option>
            <option value="3" <?php if($year==3) echo 'selected'; ?>>3</option>
            <option value="4" <?php if($year==4) echo 'selected'; ?>>4</option>
        </select>
        Filter by Semester:
        <select name="semester">
            <option value="0">All</option>
            <?php for ($i = 1; $i <= 8; $i++): ?>
                <option value="<?php echo $i; ?>" <?php if($semester==$i) echo 'selected'; ?>><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
        <input type="submit" value="Search">
    </form>
    
    <hr>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Year</th>
            <th>Semester</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo $row['year']; ?></td>
            <td><?php echo $row['semester']; ?></td>
            <td><a href="<?php echo htmlspecialchars($row['file_path']); ?>" download>Download</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>