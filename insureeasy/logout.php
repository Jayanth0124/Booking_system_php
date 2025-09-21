<?php
// 1. You must start the session before you can modify or destroy it.
session_start();

// 2. Unset all of the session variables to clear them.
session_unset();

// 3. Finally, destroy the session itself from the server.
session_destroy();

// 4. Redirect the user to the login page.
header("Location: login.php");
exit();
?>