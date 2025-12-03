<?php
// admin/users.php
require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Handle actions (approve, deactivate, activate)
if (isset($_GET['action'], $_GET['id'])) {
    $user_id = (int) $_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        $conn->query("UPDATE users SET status = 'active' WHERE user_id = $user_id");
    } elseif ($action === 'deactivate') {
        $conn->query("UPDATE users SET status = 'inactive' WHERE user_id = $user_id");
    } elseif ($action === 'activate') {
        $conn->query("UPDATE users SET status = 'active' WHERE user_id = $user_id");
    }

    redirect('users.php');
}

// Get all users
$sql = "SELECT u.user_id, u.username, u.email, u.user_type, u.status, u.created_at,
               r.first_name, r.last_name
        FROM users u
        LEFT JOIN residents r ON u.user_id = r.user_id
        ORDER BY u.created_at DESC";
$result = $conn->query($sql);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
        <h1 class="h3"><i class="bi bi-people"></i> User Accounts</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>All Users</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo trim($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo ucfirst($row['user_type']); ?></span></td>
                            <td>
                                <?php
                                    $status = $row['status'];
                                    $badge = $status === 'active' ? 'success' : ($status === 'pending' ? 'warning' : 'secondary');
                                ?>
                                <span class="badge bg-<?php echo $badge; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <?php if ($status === 'pending'): ?>
                                    <a href="?action=approve&id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-success">
                                        Approve
                                    </a>
                                <?php elseif ($status === 'active'): ?>
                                    <a href="?action=deactivate&id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-outline-danger">
                                        Deactivate
                                    </a>
                                <?php else: ?>
                                    <a href="?action=activate&id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-outline-success">
                                        Activate
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No users found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
