<?php
// resident/includes/header.php
require_once '../config/config.php';
if (!isLoggedIn() || !isResident()) {
    redirect('login.php');
}

$resident_id = (int) ($_SESSION['resident_id'] ?? 0);

// basic notifications: pending transactions + blotters status changes
$pending_txn = $conn->query(
    "SELECT COUNT(*) AS c FROM transactions 
     WHERE resident_id = $resident_id AND status IN ('pending','processing')"
)->fetch_assoc()['c'];

$open_blotter = $conn->query(
    "SELECT COUNT(*) AS c FROM blotter_reports 
     WHERE resident_id = $resident_id AND status IN ('new','ongoing')"
)->fetch_assoc()['c'];

$total_notif = $pending_txn + $open_blotter;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>eBarangay Resident Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-size: 0.9rem; background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: #212529;
            color: #fff;
        }
        .sidebar a { color:#adb5bd; text-decoration:none; padding:0.7rem 1rem; display:block; border-radius:8px; }
        .sidebar a:hover { color:#fff; background:rgba(255,255,255,0.08); }
        .sidebar .nav-link.active { background:#0d6efd; color:#fff; }
        .notification-badge { position:relative; margin-right:1.5rem; cursor:pointer; }
        .notification-badge .badge { position:absolute; top:-8px; right:-8px; font-size:0.7rem; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark sticky-top p-0 shadow">
    <div class="container-fluid">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 py-3" href="dashboard.php">
            <i class="bi bi-house-door"></i> eBarangay Resident
        </a>
        <div class="d-flex align-items-center text-white pe-3">
            <!-- Notifications -->
            <div class="dropdown notification-badge">
                <a class="text-white position-relative" data-bs-toggle="dropdown">
                    <i class="bi bi-bell-fill fs-5"></i>
                    <?php if ($total_notif > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?php echo $total_notif; ?></span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" style="width:260px;">
                    <li><h6 class="dropdown-header">Notifications (<?php echo $total_notif; ?>)</h6></li>
                    <?php if ($pending_txn > 0): ?>
                        <li><a class="dropdown-item" href="transactions.php">
                            <i class="bi bi-file-text text-info"></i>
                            <?php echo $pending_txn; ?> pending request(s)
                        </a></li>
                    <?php endif; ?>
                    <?php if ($open_blotter > 0): ?>
                        <li><a class="dropdown-item" href="blotter.php">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            <?php echo $open_blotter; ?> open blotter case(s)
                        </a></li>
                    <?php endif; ?>
                    <?php if ($total_notif == 0): ?>
                        <li><span class="dropdown-item text-muted">No new notifications</span></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Profile menu -->
            <div class="dropdown">
                <a class="text-white d-flex align-items-center" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <span class="me-2"><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?></span>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile.php">
                        <i class="bi bi-person"></i> My Profile
                    </a></li>
                    <li><a class="dropdown-item" href="transactions.php">
                        <i class="bi bi-clock-history"></i> My Requests
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
