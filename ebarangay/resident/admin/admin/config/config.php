// config/config.php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ebarangay_db');

// IMPORTANT: set this to your real base
// Example if the project folder is /ebarangay on localhost:
define('BASE_URL', 'http://localhost/ebarangay/');
// Make sure it ends with a trailing slash

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

function redirect($path) {
    // $path is relative to project root, e.g. 'login.php' or 'admin/dashboard.php'
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit();
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}
function isAdmin() {
    return !empty($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}
function isResident() {
    return !empty($_SESSION['user_type']) && $_SESSION['user_type'] === 'resident';
}
