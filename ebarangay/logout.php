<?php
// logout.php
require_once 'config/config.php';

session_unset();
session_destroy();

// Redirect to login with logout success message
header('Location: ' . BASE_URL . 'login.php?logout=1');
exit();
?>
