<?php
include 'db_connect.php';
if (isset($_POST['register'])) {
    $conn->begin_transaction();
    try {
        $otp = rand(1000, 9999);
        $stmt_user = $conn->prepare("INSERT INTO users (username, password, role, otp_code) VALUES (?, ?, 'public', ?)");
        $stmt_user->bind_param("sss", $_POST['username'], $_POST['password'], $otp);
        $stmt_user->execute();
        $user_id = $conn->insert_id;

        $stmt_pub = $conn->prepare("INSERT INTO public_users (user_id, full_name) VALUES (?, ?)");
        $stmt_pub->bind_param("is", $user_id, $_POST['full_name']);
        $stmt_pub->execute();

        $conn->commit();
        // Redirect to OTP page. In real life, OTP is sent via SMS/Email.
        header("Location: otp_verify.php?user_id=$user_id&otp=$otp"); 
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed. Username may already be in use.";
    }
}
?>
<h3>Buyer Registration</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit" name="register">Register</button>
</form>