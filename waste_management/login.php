<?php
include 'db_connect.php';

// This block runs when the login form is submitted.
if ($_SERVER["REQUEST_METHOD"] == "POST") { // <-- CORRECTED: Added '==' here
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Find the user by their username and password.
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // If a user is found, set their session details.
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['related_id'] = $user['related_id'];

        // Redirect the user to the correct dashboard based on their role.
        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($user['role'] == 'public') {
            header("Location: public_dashboard.php");
        } elseif ($user['role'] == 'driver') {
            header("Location: driver_dashboard.php");
        }
        exit();
    } else {
        // If no user is found, show an error message.
        echo "<b>Error: Invalid username or password.</b>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h3>Solid Waste Management System Login</h3>
    <form method="post" action="login.php">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <hr>
    <p>New here? <a href="register.php">Register as a Public User</a>.</p>
</body>
</html>