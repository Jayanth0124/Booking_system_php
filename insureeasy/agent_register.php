<?php
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();
    try {
        $stmt_agent = $conn->prepare("INSERT INTO agents (full_name, phone_number) VALUES (?, ?)");
        $stmt_agent->bind_param("ss", $_POST['full_name'], $_POST['phone_number']);
        $stmt_agent->execute();
        $agent_id = $conn->insert_id;

        // Create user login with is_approved = 0 (pending)
        $stmt_user = $conn->prepare("INSERT INTO users (email, password, role, related_id, is_approved) VALUES (?, ?, 'agent', ?, 0)");
        $stmt_user->bind_param("ssi", $_POST['email'], $_POST['password'], $agent_id);
        $stmt_user->execute();

        $conn->commit();
        echo "Registration successful! Your account is pending approval by the admin. You will be able to log in once approved.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Registration failed. Email might already be in use.";
    }
}
?>
<h3>Agent Registration</h3>
<form method="post">
    Full Name: <input type="text" name="full_name" required><br>
    Phone Number: <input type="text" name="phone_number" required><br>
    Login Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register as Agent</button>
</form>