<?php
// admin/includes/header.php
require_once '../config/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Get notifications count
$pending_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE status='pending'")->fetch_assoc()['count'];
$pending_transactions = $conn->query("SELECT COUNT(*) as count FROM transactions WHERE status='pending'")->fetch_assoc()['count'];
$new_blotters = $conn->query("SELECT COUNT(*) as count FROM blotter_reports WHERE status='new'")->fetch_assoc()['count'];
$total_notifications = $pending_users + $pending_transactions + $new_blotters;
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
        body { 
            font-size: 0.9rem; 
            background: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
            color: #fff;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            display: block;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: #fff;
            font-weight: 600;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
        }
        .notification-badge {
            position: relative;
            margin-right: 1.5rem;
            cursor: pointer;
        }
        .notification-badge .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            font-size: 0.7rem;
        }
        .profile-dropdown {
            cursor: pointer;
        }
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-primary sticky-top p-0 shadow">
    <div class="container-fluid">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 py-3" href="dashboard.php">
            <i class="bi bi-building"></i>
            <span>eBarangay Admin</span>
        </a>
        <div class="d-flex align-items-center text-white pe-3">
            <!-- Notifications Dropdown -->
            <div class="dropdown notification-badge">
                <a class="text-white position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell-fill fs-5"></i>
                    <?php if ($total_notifications > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?php echo $total_notifications; ?></span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                    <li><h6 class="dropdown-header">Notifications (<?php echo $total_notifications; ?>)</h6></li>
                    <?php if ($pending_users > 0): ?>
                        <li><a class="dropdown-item" href="users.php">
                            <i class="bi bi-person-plus text-warning"></i> <?php echo $pending_users; ?> pending user approval(s)
                        </a></li>
                    <?php endif; ?>
                    <?php if ($pending_transactions > 0): ?>
                        <li><a class="dropdown-item" href="transactions.php">
                            <i class="bi bi-file-text text-info"></i> <?php echo $pending_transactions; ?> pending transaction(s)
                        </a></li>
                    <?php endif; ?>
                    <?php if ($new_blotters > 0): ?>
                        <li><a class="dropdown-item" href="blotter.php">
                            <i class="bi bi-exclamation-triangle text-danger"></i> <?php echo $new_blotters; ?> new blotter report(s)
                        </a></li>
                    <?php endif; ?>
                    <?php if ($total_notifications == 0): ?>
                        <li><span class="dropdown-item text-muted">No new notifications</span></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Profile Dropdown -->
            <div class="dropdown profile-dropdown">
                <a class="text-white d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <span class="me-2"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="settings.php">
                        <i class="bi bi-gear"></i> Settings
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
