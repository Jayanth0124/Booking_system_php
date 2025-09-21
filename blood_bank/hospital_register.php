<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        $stmt_hosp = $conn->prepare("INSERT INTO hospitals (hospital_name, address, city) VALUES (?, ?, ?)");
        $stmt_hosp->bind_param("sss", $_POST['hospital_name'], $_POST['address'], $_POST['city']);
        $stmt_hosp->execute();
        $hospital_id = $conn->insert_id;

        // Create user login with is_approved = 0 (pending)
        $stmt_user = $conn->prepare("INSERT INTO users (email, password, role, related_id, is_approved) VALUES (?, ?, 'hospital', ?, 0)");
        $stmt_user->bind_param("ssi", $_POST['email'], $_POST['password'], $hospital_id);
        $stmt_user->execute();
        
        $conn->commit();
        echo "Registration successful! Your hospital will be visible after admin approval. You can log in once approved.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed. Email may already be in use.";
    }
}
?>
<h3>Hospital/Blood Bank Registration</h3>
<form method="post">
    Hospital Name: <input type="text" name="hospital_name" required><br>
    Address: <textarea name="address" required></textarea><br>
    City: <input type="text" name="city" required><br>
    Login Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register Hospital</button>
</form>