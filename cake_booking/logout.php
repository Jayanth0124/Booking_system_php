<?php
// 1. You must start the session before you can destroy it.
session_start();

// 2. Unset all of the session variables.
session_unset();

// 3. Finally, destroy the session.
session_destroy();

// 4. Redirect the user to the login page after logging out.
header("Location: login.php");
exit();
?>