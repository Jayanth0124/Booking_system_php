<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, email, role, related_id FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['related_id'] = $user['related_id'];

        if ($user['role'] == 'admin') header("Location: admin_dashboard.php");
        elseif ($user['role'] == 'patient') header("Location: patient_dashboard.php");
        elseif ($user['role'] == 'hospital') header("Location: hospital_dashboard.php");
        exit();
    } else {
        echo "Invalid email or password.";
    }
}
?>
<h3>Login to MedCare</h3>
<form method="post">
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>
<a href="register.php">Don't have an account? Register</a>