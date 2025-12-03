<?php
// config/config.php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ebarangay_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Define base URL
define('BASE_URL', 'http://localhost/ebarangay/');

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Function to check if user is resident
function isResident() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'resident';
}

// Function to redirect
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}
?>
