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
        if ($user['role'] == 'agent' && $user['is_approved'] == 0) {
            echo "Your agent account is pending approval from the administrator.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['related_id'] = $user['related_id'];

            if ($user['role'] == 'admin') header("Location: admin_dashboard.php");
            elseif ($user['role'] == 'agent') header("Location: agent_dashboard.php");
            elseif ($user['role'] == 'customer') header("Location: customer_dashboard.php");
            exit();
        }
    } else {
        echo "Invalid email or password.";
    }
}
?>
<h3>InsureEasy Login</h3>
<form method="post">
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>
<p>Are you an insurance agent? <a href="agent_register.php">Register here</a>.</p>