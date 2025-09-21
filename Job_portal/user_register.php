<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        $stmt_app = $conn->prepare("INSERT INTO applicants (full_name, phone_number) VALUES (?, ?)");
        $stmt_app->bind_param("ss", $_POST['full_name'], $_POST['phone_number']);
        $stmt_app->execute();
        $applicant_id = $conn->insert_id;

        $stmt_user = $conn->prepare("INSERT INTO users (email, password, role, related_id, is_approved) VALUES (?, ?, 'user', ?, 1)");
        $stmt_user->bind_param("ssi", $_POST['email'], $_POST['password'], $applicant_id);
        $stmt_user->execute();

        $conn->commit();
        echo "Registration successful! <a href='login.php'>Login here</a>.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed. Email might already be in use.";
    }
}
?>
<h3>User (Job Seeker) Registration</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Phone Number: <input type="text" name="phone_number" required><br>
    Login Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register as Job Seeker</button>
</form>