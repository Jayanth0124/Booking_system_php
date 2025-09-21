<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Special check for Director approval
        if ($user['role'] == 'director' && $user['is_approved'] == 0) {
            echo "<b>Your Director account is pending approval from the administrator. You cannot log in yet.</b>";
        } else {
            // Set session variables for the logged-in user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['related_id'] = $user['related_id'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] == 'user') {
                header("Location: user_dashboard.php");
            } elseif ($user['role'] == 'director') {
                header("Location: director_dashboard.php");
            }
            exit();
        }
    } else {
        echo "<b>Error: Invalid email or password.</b>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h3>Job Portal Login</h3>
    <form method="post">
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <hr>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>