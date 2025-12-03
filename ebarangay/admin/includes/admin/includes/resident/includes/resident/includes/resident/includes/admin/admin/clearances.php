<?php
// admin/clearances.php
require_once '../config/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$message = '';

// Issue new clearance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issue_clearance'])) {
    $resident_id = (int) $_POST['resident_id'];
    $clearance_type = $conn->real_escape_string($_POST['clearance_type']);
    $purpose = $conn->real_escape_string($_POST['purpose']);
    $or_number = $conn->real_escape_string($_POST['or_number']);
    $amount = (float) $_POST['amount'];
    $issued_by = (int) $_SESSION['user_id'];
    
    $sql = "INSERT INTO clearances (resident_id, clearance_type, purpose, or_number, amount, issued_by, issued_date)
            VALUES ($resident_id, '$clearance_type', '$purpose', '$or_number', $amount, $issued_by, NOW())";
    
    if ($conn->query($sql)) {
        $message = 'Clearance issued successfully.';
    } else {
        $message = 'Error issuing clearance.';
    }
}

// Create clearances table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS clearances (
    clearance_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    resident_id INT(11),
    clearance_type VARCHAR(100) NOT NULL,
    purpose TEXT,
    or_number VARCHAR(50),
    amount DECIMAL(10,2),
    issued_by INT(11),
    issued_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valid_until DATE,
    status ENUM('active','expired','cancelled') DEFAULT 'active',
    FOREIGN KEY (resident_id) REFERENCES residents(resident_id),
    FOREIGN KEY (issued_by) REFERENCES users(user_id)
)");

// Get all clearances
$clearances = $conn->query("SELECT c.*, r.first_name, r.last_name, u.username as issued_by_name
                            FROM clearances c
                            JOIN residents r ON c.resident_id = r.resident_id
                            JOIN users u ON c.issued_by = u.user_id
                            ORDER BY c.issued_date DESC");

// Get all residents for dropdown
$residents = $conn->query("SELECT resident_id, first_name, middle_name, last_name FROM residents ORDER BY last_name");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-file-earmark-check"></i> Clearances Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issueClearanceModal">
            <i class="bi bi-plus-circle"></i> Issue New Clearance
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-light">
            <strong>All Clearances</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Clearance #</th>
                            <th>Resident</th>
                            <th>Type</th>
                            <th>Purpose</th>
                            <th>OR Number</th>
                            <th>Amount</th>
                            <th>Issued Date</th>
                            <th>Issued By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($clearances->num_rows > 0): ?>
                        <?php while ($row = $clearances->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($row['clearance_id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($row['clearance_type']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                            <td><?php echo htmlspecialchars($row['or_number']); ?></td>
                            <td>₱<?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['issued_date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['issued_by_name']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="printClearance(<?php echo $row['clearance_id']; ?>)">
                                    <i class="bi bi-printer"></i> Print
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">No clearances issued yet.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Issue Clearance Modal -->
<div class="modal fade" id="issueClearanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-check"></i> Issue New Clearance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Select Resident *</label>
                            <select name="resident_id" class="form-control" required>
                                <option value="">Choose resident...</option>
                                <?php 
                                $residents->data_seek(0);
                                while ($res = $residents->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $res['resident_id']; ?>">
                                        <?php echo htmlspecialchars($res['first_name'] . ' ' . $res['middle_name'] . ' ' . $res['last_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Clearance Type *</label>
                            <select name="clearance_type" class="form-control" required>
                                <option value="">Select type...</option>
                                <option value="Barangay Clearance">Barangay Clearance</option>
                                <option value="Certificate of Indigency">Certificate of Indigency</option>
                                <option value="Certificate of Residency">Certificate of Residency</option>
                                <option value="Good Moral Certificate">Good Moral Certificate</option>
                                <option value="Business Clearance">Business Clearance</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Purpose *</label>
                            <input type="text" name="purpose" class="form-control" required placeholder="e.g., Employment, School requirement">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">OR Number *</label>
                            <input type="text" name="or_number" class="form-control" required placeholder="Official Receipt Number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Amount (₱) *</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="issue_clearance" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Issue Clearance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function printClearance(id) {
    window.open('print_clearance.php?id=' + id, '_blank');
}
</script>

<?php include 'includes/footer.php'; ?>
