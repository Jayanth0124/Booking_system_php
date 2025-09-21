<?php
include 'db_connect.php';
if (isset($_POST['login'])) {
    $stmt = $conn->prepare("SELECT * FROM voters WHERE voter_id_number = ? AND password = ?");
    $stmt->bind_param("ss", $_POST['voter_id'], $_POST['password']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($voter = $result->fetch_assoc()) {
        $otp = rand(1000, 9999);
        $conn->query("UPDATE voters SET otp_code = '$otp' WHERE id = ".$voter['id']);
        // Redirect to OTP page. In real life, OTP is sent via SMS.
        header("Location: otp_verify.php?voter_id=".$voter['id']."&otp=$otp"); 
        exit();
    } else {
        echo "Invalid Voter ID or Password.";
    }
}
?>
<h3>Voter Login</h3>
<form method="post">
    Voter ID Number: <input type="text" name="voter_id" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit" name="login">Login</button>
</form>
<p>Admin? <a href="admin_login.php">Login here</a>.</p>