<?php
include 'db_connect.php';
if (isset($_POST['verify_otp'])) {
    $stmt = $conn->prepare("SELECT * FROM voters WHERE id = ? AND otp_code = ?");
    $stmt->bind_param("is", $_POST['voter_id'], $_POST['otp']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($voter = $result->fetch_assoc()) {
        $_SESSION['voter_db_id'] = $voter['id'];
        $_SESSION['voter_id_number'] = $voter['voter_id_number'];
        $conn->query("UPDATE voters SET otp_code = NULL WHERE id = ".$voter['id']);
        header("Location: voter_dashboard.php");
        exit();
    } else {
        echo "Invalid OTP.";
    }
}
?>
<h3>Enter OTP to Verify Login</h3>
<p>For this simulation, your OTP is: <b><?php echo $_GET['otp']; ?></b></p>
<form method="post">
    <input type="hidden" name="voter_id" value="<?php echo $_GET['voter_id']; ?>">
    Enter OTP: <input type="text" name="otp" required>
    <button type="submit" name="verify_otp">Verify</button>
</form>