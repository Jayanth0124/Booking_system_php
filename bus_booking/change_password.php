<?php
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    // Verify old password
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND password = ?");
    $stmt->bind_param("is", $_SESSION['user_id'], $old_password);
    $stmt->execute();
    if ($stmt->get_result()->num_rows == 1) {
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_password, $_SESSION['user_id']);
        if ($update_stmt->execute()) {
            echo "Password changed successfully!";
        } else {
            echo "Error updating password.";
        }
    } else {
        echo "Incorrect old password.";
    }
}
?>
<h3>Change Password</h3>
<form method="post">
    Old Password: <input type="password" name="old_password" required><br>
    New Password: <input type="password" name="new_password" required><br>
    <button type="submit">Change Password</button>
</form>