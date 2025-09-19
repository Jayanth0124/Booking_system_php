<?php
// Start the session to access it
session_start();

// Unset all session variables
session_unset();

// Destroy the session itself
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit();
?>