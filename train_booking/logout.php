<?php
// 1. Resume the existing session
session_start();

// 2. Unset all session variables to clear them
session_unset();

// 3. Destroy the session itself
session_destroy();

// 4. Redirect the user to the login page
header("Location: login.php");
exit();
?>