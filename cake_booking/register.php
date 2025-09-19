<?php
include 'db_connect.php';

// This block executes when the user submits the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // For simplicity in this project, we are storing passwords as plain text.
    // For a real website, ALWAYS use password_hash() for security.
    $password = $_POST['password'];

    // 1. Check if the username or email already exists to prevent duplicates
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If a user is found, display an error message
        echo "Error: This username or email is already registered.";
    } else {
        // 2. If the user is unique, insert their details into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            echo "Registration successful! You can now <a href='login.php'>log in</a>.";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h3>Create a New Account</h3>
    <form method="post" action="register.php">
        Username: <input type="text" name="username" required><br><br>
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Register</button>
    </form>
    <hr>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>