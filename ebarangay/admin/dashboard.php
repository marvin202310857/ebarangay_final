<?php
// admin/dashboard.php
require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Get statistics
$total_residents = $conn->query("SELECT COUNT(*) as count FROM residents")->fetch_assoc()['count'];
$pending_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'pending'")->fetch_assoc()['count'];
$pending_transactions = $conn->query("SELECT COUNT(*) as count FROM transactions WHERE status = 'pending'")->fetch_assoc()['count'];
$active_blotters = $conn->query("SELECT COUNT(*) as count FROM blotter_reports WHERE status IN ('new', 'ongoing')")->fetch_assoc()['count'];
$pending_pets = $conn->query("SELECT COUNT(*) as count FROM pet_registrations WHERE status = 'pending'")->fetch_assoc()['count'];

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-people"></i> Total Residents</h5>
                            <h2><?php echo $total_residents; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-person-check"></i> Pending Approvals</h5>
                            <h2><?php echo $pending_users; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-file-text"></i> Pending Transactions</h5>
                            <h2><?php echo $pending_transactions; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-exclamation-triangle"></i> Active Blotters</h5>
                            <h2><?php echo $active_blotters; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-person-plus"></i> Recent Registration Requests</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $sql = "SELECT u.user_id, u.username, u.email, r.first_name, r.last_name, u.created_at 
                                    FROM users u 
                                    JOIN residents r ON u.user_id = r.user_id 
                                    WHERE u.status = 'pending' 
                                    ORDER BY u.created_at DESC 
                                    LIMIT 5";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0):
                            ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                            <td><?php echo $row['username']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <a href="users.php?action=view&id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <p class="text-muted">No pending registrations</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-file-earmark-text"></i> Recent Transactions</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $sql = "SELECT t.*, r.first_name, r.last_name 
                                    FROM transactions t 
                                    JOIN residents r ON t.resident_id = r.resident_id 
                                    ORDER BY t.requested_date DESC 
                                    LIMIT 5";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0):
                            ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Resident</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                                            <td><?php echo $row['transaction_type']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $row['status'] == 'approved' ? 'success' : 
                                                        ($row['status'] == 'pending' ? 'warning' : 'info'); 
                                                ?>">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="transactions.php?action=view&id=<?php echo $row['transaction_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <p class="text-muted">No transactions yet</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
