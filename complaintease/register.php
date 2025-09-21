<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        $stmt_comp = $conn->prepare("INSERT INTO complainants (full_name, phone_number) VALUES (?, ?)");
        $stmt_comp->bind_param("ss", $_POST['full_name'], $_POST['phone_number']);
        $stmt_comp->execute();
        $complainant_id = $conn->insert_id;

        $stmt_user = $conn->prepare("INSERT INTO users (email, password, role, related_id) VALUES (?, ?, 'user', ?)");
        $stmt_user->bind_param("ssi", $_POST['email'], $_POST['password'], $complainant_id);
        $stmt_user->execute();

        $conn->commit();
        echo "Registration successful! <a href='login.php'>Login here</a>.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed. Email might already be in use.";
    }
}
?>
<h3>User Registration</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Phone Number: <input type="text" name="phone_number" required><br>
    Login Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>