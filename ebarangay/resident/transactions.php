<?php
// resident/transactions.php
require_once '../config/config.php';

if (!isLoggedIn() || !isResident()) {
    redirect('../login.php');
}

$resident_id = (int) ($_SESSION['resident_id'] ?? 0);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_type = $conn->real_escape_string($_POST['transaction_type']);
    $purpose = $conn->real_escape_string($_POST['purpose']);

    $sql = "INSERT INTO transactions (resident_id, transaction_type, purpose)
            VALUES ($resident_id, '$transaction_type', '$purpose')";
    if ($conn->query($sql)) {
        $message = 'Request submitted successfully. Please wait for approval.';
    } else {
        $message = 'Error submitting request. Please try again.';
    }
}

$transactions = $conn->query("SELECT * FROM transactions WHERE resident_id = $resident_id ORDER BY requested_date DESC");
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-file-text"></i> My Transactions</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><strong>New document request</strong></div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Type *</label>
                        <select name="transaction_type" class="form-control" required>
                            <option value="">Select type</option>
                            <option value="Barangay Clearance">Barangay Clearance</option>
                            <option value="Certificate of Indigency">Certificate of Indigency</option>
                            <option value="Business Permit">Business Permit</option>
                            <option value="Residency Certificate">Residency Certificate</option>
                        </select>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Purpose *</label>
                        <textarea name="purpose" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i> Submit request
                </button>
            </form>
        </div>
    </div>

    <h5 class="mb-3">My requests</h5>
    <div class="card">
        <div class="card-body">
            <?php if ($transactions->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Requested</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; while ($row = $transactions->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
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
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No requests yet.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
