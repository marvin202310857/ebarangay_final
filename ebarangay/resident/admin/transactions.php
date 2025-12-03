<?php
// admin/transactions.php
require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];
    $status = $action === 'approve' ? 'approved' : ($action === 'reject' ? 'rejected' : null);

    if ($status) {
        $remarks = $conn->real_escape_string($_GET['remarks'] ?? '');
        $user_id = (int) $_SESSION['user_id'];

        $sql = "UPDATE transactions 
                SET status = '$status',
                    processed_by = $user_id,
                    processed_date = NOW(),
                    remarks = '$remarks'
                WHERE transaction_id = $id";
        $conn->query($sql);
    }

    redirect('transactions.php');
}

$sql = "SELECT t.*, r.first_name, r.last_name 
        FROM transactions t 
        JOIN residents r ON t.resident_id = r.resident_id
        ORDER BY t.requested_date DESC";
$transactions = $conn->query($sql);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-file-earmark-text"></i> All Transactions</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if ($transactions->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Resident</th>
                            <th>Type</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th>Processed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; while ($row = $transactions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['transaction_type']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['purpose'])); ?></td>
                            <td>
                                <?php
                                    $status = $row['status'];
                                    $badge = $status === 'approved' ? 'success' :
                                             ($status === 'rejected' ? 'danger' :
                                             ($status === 'processing' ? 'info' : 'warning'));
                                ?>
                                <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['requested_date'])); ?></td>
                            <td>
                                <?php echo $row['processed_date'] ? date('M d, Y', strtotime($row['processed_date'])) : '-'; ?>
                            </td>
                            <td>
                                <?php if ($status === 'pending' || $status === 'processing'): ?>
                                    <a href="transactions.php?action=approve&id=<?php echo $row['transaction_id']; ?>" 
                                       class="btn btn-sm btn-success mb-1">Approve</a>
                                    <a href="transactions.php?action=reject&id=<?php echo $row['transaction_id']; ?>" 
                                       class="btn btn-sm btn-danger mb-1">Reject</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p class="text-muted mb-0">No transactions found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
