<?php
include 'db_connect.php';
if (isset($_POST['admin_login'])) {
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $_POST['username'], $_POST['password']);
    $stmt->execute();
    if ($admin = $stmt->get_result()->fetch_assoc()) {
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: admin_dashboard.php");
        exit();
    } else { echo "Invalid Admin credentials."; }
}
?>
<h3>Admin Login</h3>
<form method="post">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit" name="admin_login">Login</button>
</form>