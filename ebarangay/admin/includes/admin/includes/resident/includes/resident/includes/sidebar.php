<?php
// resident/includes/sidebar.php
$current = basename($_SERVER['PHP_SELF']);
?>
<aside class="col-md-2 d-md-block sidebar py-3">
    <ul class="nav flex-column px-2">
        <li class="nav-item mb-1">
            <a class="nav-link <?php if ($current === 'dashboard.php') echo 'active'; ?>" href="dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link <?php if ($current === 'transactions.php') echo 'active'; ?>" href="transactions.php">
                <i class="bi bi-file-text"></i> Transactions
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link <?php if ($current === 'blotter.php') echo 'active'; ?>" href="blotter.php">
                <i class="bi bi-exclamation-triangle"></i> Blotter
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link <?php if ($current === 'pets.php') echo 'active'; ?>" href="pets.php">
                <i class="bi bi-paw"></i> Pets
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link <?php if ($current === 'profile.php') echo 'active'; ?>" href="profile.php">
                <i class="bi bi-person"></i> My profile
            </a>
        </li>
    </ul>
</aside>
