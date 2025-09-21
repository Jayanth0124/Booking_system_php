<?php
include 'db_connect.php';
if (isset($_POST['login'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $_POST['username'], $_POST['password']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        if ($user['is_verified'] == 0) {
            echo "Please verify your account via OTP first.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] == 'admin') header("Location: admin_dashboard.php");
            elseif ($user['role'] == 'farmer') header("Location: farmer_dashboard.php");
            else header("Location: index.php");
            exit();
        }
    } else {
        echo "Invalid username or password.";
    }
}
?>
<h3>Login to AgriMart</h3>
<form method="post">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit" name="login">Login</button>
</form>
<a href="register.php">Create an Account</a>