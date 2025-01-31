<?php
session_start();

$_SESSION['admin_status'] = 'inactive';

session_unset();
session_destroy();

header("Location: admin_login.php");
exit;
?>
