<?php
// admin/includes/header.php
require_once '../config/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eBarangay Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-size: 0.9rem; }
        .sidebar {
            min-height: 100vh;
            background: #0d6efd;
            color: #fff;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            border-radius: .25rem;
        }
        .navbar-brand span {
            font-weight: 600;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-primary sticky-top flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="dashboard.php">
        <i class="bi bi-building"></i>
        <span>eBarangay Admin</span>
    </a>
    <div class="w-100 text-end pe-3 text-white">
        <span class="me-3"><i class="bi bi-person-circle"></i>
            <?php echo htmlspecialchars($_SESSION['username']); ?>
        </span>
        <a href="../logout.php" class="text-white text-decoration-none">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
