<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; // For a real site, ALWAYS use password_hash()

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Error: This username is already taken.";
    } else {
        // Insert new user with the default 'user' role
        $stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
        $stmt_insert->bind_param("ss", $username, $password);
        
        if ($stmt_insert->execute()) {
            echo "Registration successful! You can now <a href='login.php'>log in</a>.";
        } else {
            echo "Error: Could not register account.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
    <h3>Create a New Account</h3>
    <form method="post">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Register</button>
    </form>
    <hr>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>