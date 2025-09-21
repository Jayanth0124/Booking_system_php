<?php
include 'db_connect.php';
if (isset($_POST['register'])) {
    $conn->begin_transaction();
    try {
        $otp = rand(1000, 9999);
        $stmt_user = $conn->prepare("INSERT INTO users (username, password, role, otp_code, is_verified) VALUES (?, ?, 'farmer', ?, 0)");
        $stmt_user->bind_param("sss", $_POST['username'], $_POST['password'], $otp);
        $stmt_user->execute();
        $user_id = $conn->insert_id;

        $stmt_farmer = $conn->prepare("INSERT INTO farmers (user_id, full_name, farm_name, phone_number) VALUES (?, ?, ?, ?)");
        $stmt_farmer->bind_param("isss", $user_id, $_POST['full_name'], $_POST['farm_name'], $_POST['phone_number']);
        $stmt_farmer->execute();

        $conn->commit();
        header("Location: otp_verify.php?user_id=$user_id&otp=$otp"); 
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed. Username may already be in use.";
    }
}
?>
<h3>Farmer Registration</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Farm/Business Name: <input type="text" name="farm_name" required><br>
    Phone Number: <input type="text" name="phone_number" required><br>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit" name="register">Register as Farmer</button>
</form>