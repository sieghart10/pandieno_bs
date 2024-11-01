<?php
session_start();

// Set the admin status to "inactive"
$_SESSION['admin_status'] = 'inactive';

// Destroy the session and unset all session variables
session_unset();
session_destroy();

// Redirect to the admin login page
header("Location: admin_login.php");
exit;
?>
