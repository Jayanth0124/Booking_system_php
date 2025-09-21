<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        $stmt_dir = $conn->prepare("INSERT INTO directors (company_name, contact_person) VALUES (?, ?)");
        $stmt_dir->bind_param("ss", $_POST['company_name'], $_POST['contact_person']);
        $stmt_dir->execute();
        $director_id = $conn->insert_id;

        $stmt_user = $conn->prepare("INSERT INTO users (email, password, role, related_id, is_approved) VALUES (?, ?, 'director', ?, 0)");
        $stmt_user->bind_param("ssi", $_POST['email'], $_POST['password'], $director_id);
        $stmt_user->execute();

        $conn->commit();
        echo "Registration successful! Your account is pending approval by the admin.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed. Email might already be in use.";
    }
}
?>
<h3>Director (Employer) Registration</h3>
<form method="post">
    Company Name: <input type="text" name="company_name" required><br>
    Contact Person: <input type="text" name="contact_person" required><br>
    Login Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register as Director</button>
</form>