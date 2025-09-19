<?php
// 1. Start or resume the existing session.
session_start();

// 2. Unset all of the session variables.
session_unset();

// 3. Destroy the session itself.
session_destroy();

// 4. Redirect the user to the login page.
header("Location: login.php");
exit();
?>