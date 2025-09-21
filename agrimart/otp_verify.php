<?php
include 'db_connect.php';
if (isset($_POST['verify_otp'])) {
    $stmt = $conn->prepare("SELECT otp_code FROM users WHERE id = ?");
    $stmt->bind_param("i", $_POST['user_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result && $result['otp_code'] == $_POST['otp']) {
        $conn->query("UPDATE users SET is_verified = 1, otp_code = NULL WHERE id = ".$_POST['user_id']);
        echo "Verification successful! You can now <a href='login.php'>log in</a>.";
    } else {
        echo "Invalid OTP. Please try again.";
    }
}
?>
<h3>Enter OTP</h3>
<p>For this simulation, the OTP is: <b><?php echo $_GET['otp']; ?></b></p>
<form method="post">
    <input type="hidden" name="user_id" value="<?php echo $_GET['user_id']; ?>">
    Enter OTP: <input type="text" name="otp" required>
    <button type="submit" name="verify_otp">Verify</button>
</form>