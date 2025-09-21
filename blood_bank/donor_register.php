<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        // 1. Create donor profile
        $stmt_donor = $conn->prepare("INSERT INTO donors (full_name, blood_group, phone_number, city) VALUES (?, ?, ?, ?)");
        $stmt_donor->bind_param("ssss", $_POST['full_name'], $_POST['blood_group'], $_POST['phone_number'], $_POST['city']);
        $stmt_donor->execute();
        $donor_id = $conn->insert_id;

        // 2. Create user login (approved by default)
        $stmt_user = $conn->prepare("INSERT INTO users (email, password, role, related_id, is_approved) VALUES (?, ?, 'donor', ?, 1)");
        $stmt_user->bind_param("ssi", $_POST['email'], $_POST['password'], $donor_id);
        $stmt_user->execute();

        $conn->commit();
        echo "Registration successful! <a href='login.php'>Login here</a>.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed. Email might already be in use.";
    }
}
?>
<h3>Donor Registration</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Blood Group: 
    <select name="blood_group" required>
        <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
        <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
    </select><br>
    Phone Number: <input type="text" name="phone_number" required><br>
    City: <input type="text" name="city" required><br>
    Login Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register as Donor</button>
</form>