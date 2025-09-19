<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        // 1. Create hospital profile
        $stmt_hospital = $conn->prepare("INSERT INTO hospitals (hospital_name, address, city) VALUES (?, ?, ?)");
        $stmt_hospital->bind_param("sss", $_POST['hospital_name'], $_POST['address'], $_POST['city']);
        $stmt_hospital->execute();
        $hospital_id = $conn->insert_id;

        // 2. Create user login for the hospital
        $stmt_user = $conn->prepare("INSERT INTO users (email, password, role, related_id) VALUES (?, ?, 'hospital', ?)");
        $stmt_user->bind_param("ssi", $_POST['email'], $_POST['password'], $hospital_id);
        $stmt_user->execute();
        
        $conn->commit();
        echo "Registration successful! Your hospital will be visible after admin approval. <a href='login.php'>Login here</a> to update details.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed. Email might already be in use.";
    }
}
?>
<h3>Hospital Registration</h3>
<form method="post">
    Hospital Name: <input type="text" name="hospital_name" required><br>
    Address: <input type="text" name="address" required><br>
    City: <input type="text" name="city" required><br>
    Login Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register Hospital</button>
</form>