<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];
    $password = $_POST['password'];

    // Check for duplicates
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo "Error: Username or email already exists.";
    } else {
        // Insert new user with 'customer' role
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, phone_number, role) VALUES (?, ?, ?, ?, 'customer')");
        $stmt->bind_param("ssss", $username, $email, $password, $phone);
        if ($stmt->execute()) {
            echo "Registration successful! <a href='login.php'>You can now log in.</a>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
    <h3>Create a Customer Account</h3>
    <form method="post">
        Username: <input type="text" name="username" required><br>
        Email: <input type="email" name="email" required><br>
        Phone Number: <input type="text" name="phone_number"><br>
        Password: <input type="password" name="password" required><br>
        <button type="submit">Register</button>
    </form>
    <hr>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>