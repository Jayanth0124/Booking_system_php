<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        $stmt_pub = $conn->prepare("INSERT INTO public_users (full_name, phone_number) VALUES (?, ?)");
        $stmt_pub->bind_param("ss", $_POST['full_name'], $_POST['phone_number']);
        $stmt_pub->execute();
        $public_user_id = $conn->insert_id;

        $stmt_user = $conn->prepare("INSERT INTO users (username, password, role, related_id) VALUES (?, ?, 'public', ?)");
        $stmt_user->bind_param("ssi", $_POST['username'], $_POST['password'], $public_user_id);
        $stmt_user->execute();

        $conn->commit();
        echo "Registration successful! <a href='login.php'>Login here</a>.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed. Username may already be in use.";
    }
}
?>
<h3>Public User Registration</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Phone Number: <input type="text" name="phone_number" required><br>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="login.php">Login here</a>.</p>