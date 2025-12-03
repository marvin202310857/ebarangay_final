<?php
// admin/includes/sidebar.php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="col-md-2 d-md-block sidebar py-4">
    <div class="px-3">
        <h6 class="text-white-50 text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Main Menu</h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'dashboard.php') echo 'active'; ?>" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'users.php') echo 'active'; ?>" href="users.php">
                    <i class="bi bi-people"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'residents.php') echo 'active'; ?>" href="residents.php">
                    <i class="bi bi-person-vcard"></i> Residents
                </a>
            </li>
        </ul>
        
        <h6 class="text-white-50 text-uppercase mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 1px;">Services</h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'transactions.php') echo 'active'; ?>" href="transactions.php">
                    <i class="bi bi-file-earmark-text"></i> Transactions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'clearances.php') echo 'active'; ?>" href="clearances.php">
                    <i class="bi bi-file-earmark-check"></i> Clearances
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'blotter.php') echo 'active'; ?>" href="blotter.php">
                    <i class="bi bi-journal-medical"></i> Blotter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'pets.php') echo 'active'; ?>" href="pets.php">
                    <i class="bi bi-paw"></i> Pet Registration
                </a>
            </li>
        </ul>
        
        <h6 class="text-white-50 text-uppercase mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 1px;">Management</h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'officials.php') echo 'active'; ?>" href="officials.php">
                    <i class="bi bi-person-badge"></i> Officials
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'household.php') echo 'active'; ?>" href="household.php">
                    <i class="bi bi-house-door"></i> Household
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'tanod.php') echo 'active'; ?>" href="tanod.php">
                    <i class="bi bi-shield-check"></i> Tanod Tracking
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'surveillance.php') echo 'active'; ?>" href="surveillance.php">
                    <i class="bi bi-camera-video"></i> Surveillance
                </a>
            </li>
        </ul>
        
        <h6 class="text-white-50 text-uppercase mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 1px;">System</h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'announcements.php') echo 'active'; ?>" href="announcements.php">
                    <i class="bi bi-megaphone"></i> Announcements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'reports.php') echo 'active'; ?>" href="reports.php">
                    <i class="bi bi-graph-up"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($current === 'settings.php') echo 'active'; ?>" href="settings.php">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </li>
        </ul>
    </div>
</aside>
