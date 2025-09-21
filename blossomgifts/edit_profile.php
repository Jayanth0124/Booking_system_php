<?php
include 'db_connect.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle form submission for profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    // For a real-world application, ALWAYS use password_hash() for security.
    // However, following the existing pattern in login.php and register.php where password is not hashed
    // we'll stick to a simple update. A real-world application would also re-verify the current password.
    
    // Check if username or email already exists for another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Error: The username or email is already taken by another user.";
    } else {
        // Construct the UPDATE query
        if (!empty($new_password)) {
            // Update username, email, and password
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $new_username, $new_email, $new_password, $user_id);
        } else {
            // Update only username and email
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
        }

        if ($stmt->execute()) {
            // Update the session variables
            $_SESSION['username'] = $new_username;
            $message = "Your profile has been updated successfully!";
        } else {
            $message = "Error: Could not update your profile.";
        }
    }
}

// Fetch current user details to pre-fill the form
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
</head>
<body>
    <a href="my_account.php">Back to My Account</a>
    <h1>Edit Profile</h1>

    <?php if ($message): ?>
        <p><b><?php echo htmlspecialchars($message); ?></b></p>
    <?php endif; ?>

    <form method="post">
        Username: <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br><br>
        Email: <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>
        New Password: <input type="password" name="password" placeholder="Leave blank to keep current"><br><br>
        <button type="submit">Update Profile</button>
    </form>
</body>
</html>