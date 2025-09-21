<?php
include 'db_connect.php';

// Ensure a destination ID is provided in the URL
if (!isset($_GET['dest_id'])) {
    header("Location: index.php");
    exit();
}
$dest_id = $_GET['dest_id'];

// Fetch the destination name to use as a title
$dest_stmt = $conn->prepare("SELECT destination_name FROM destinations WHERE id = ?");
$dest_stmt->bind_param("i", $dest_id);
$dest_stmt->execute();
$destination = $dest_stmt->get_result()->fetch_assoc();

// Fetch all food items linked to this destination
$food_stmt = $conn->prepare("SELECT * FROM foods WHERE destination_id = ?");
$food_stmt->bind_param("i", $dest_id);
$food_stmt->execute();
$foods = $food_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Food in <?php echo htmlspecialchars($destination['destination_name']); ?></title>
</head>
<body>
    <a href="destination_details.php?id=<?php echo $dest_id; ?>">← Back to Destination Details</a>
    <h2>Famous Food in <?php echo htmlspecialchars($destination['destination_name']); ?></h2>

    <?php if ($foods->num_rows > 0): ?>
        <?php while ($food = $foods->fetch_assoc()): ?>
            <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; max-width: 500px;">
                <img src="<?php echo htmlspecialchars($food['image_path']); ?>" alt="<?php echo htmlspecialchars($food['food_name']); ?>" width="150" style="float:left; margin-right: 15px;">
                <h3><?php echo htmlspecialchars($food['food_name']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($food['description'])); ?></p>
                <div style="clear:both;"></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No food details have been added for this destination yet.</p>
    <?php endif; ?>
</body>
</html>