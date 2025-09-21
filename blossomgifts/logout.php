<?php
// You must start the session to be able to access and destroy it.
session_start();

// Unset all session variables to clear the user's data.
session_unset();

// Destroy the session itself from the server.
session_destroy();

// Redirect the user to the login page.
header("Location: login.php");
exit();
?>