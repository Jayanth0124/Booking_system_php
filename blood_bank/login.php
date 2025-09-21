<?php
include 'db_connect.php';

// This block runs when the login form is submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Find the user by email and password.
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // SPECIAL CHECK: If the user is a hospital, check if they are approved.
        if ($user['role'] == 'hospital' && $user['is_approved'] == 0) {
            echo "<b>Your hospital account is pending approval from the administrator. You cannot log in yet.</b>";
        } else {
            // If approved (or any other role), set session variables.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['related_id'] = $user['related_id'];

            // Redirect to the correct dashboard based on the user's role.
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] == 'donor') {
                header("Location: donor_dashboard.php");
            } elseif ($user['role'] == 'hospital') {
                header("Location: hospital_dashboard.php");
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
    <h3>Blood Bank System Login</h3>
    <form method="post">
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <hr>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</body>
</html>