<?php
require_once '../config/config.php';
if (!isLoggedIn() || !isResident()) {
    redirect('login.php');
}

$resident_id = (int) ($_SESSION['resident_id'] ?? 0);

// counts for quick cards
$my_txn = $conn->query("SELECT COUNT(*) AS c FROM transactions WHERE resident_id=$resident_id")->fetch_assoc()['c'];
$pending_txn = $conn->query("SELECT COUNT(*) AS c FROM transactions WHERE resident_id=$resident_id AND status='pending'")->fetch_assoc()['c'];
$approved_txn = $conn->query("SELECT COUNT(*) AS c FROM transactions WHERE resident_id=$resident_id AND status='approved'")->fetch_assoc()['c'];
$my_blotter = $conn->query("SELECT COUNT(*) AS c FROM blotter_reports WHERE resident_id=$resident_id")->fetch_assoc()['c'];
$my_pets = $conn->query("SELECT COUNT(*) AS c FROM pet_registrations WHERE resident_id=$resident_id")->fetch_assoc()['c'];

// latest 5 requests
$req = $conn->query("SELECT * FROM transactions WHERE resident_id=$resident_id ORDER BY requested_date DESC LIMIT 5");

// latest announcements
$ann = $conn->query("SELECT * FROM announcements WHERE status='published' ORDER BY posted_date DESC LIMIT 5");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
        <h1 class="h3"><i class="bi bi-speedometer2"></i> My Dashboard</h1>
        <span class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
    </div>

    <!-- Status cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted">Total Requests</h6>
                    <h2><?php echo $my_txn; ?></h2>
                    <small><a href="transactions.php">View all requests</a></small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="text-muted">Pending Requests</h6>
                    <h2><?php echo $pending_txn; ?></h2>
                    <small>Waiting for barangay approval</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">Approved Requests</h6>
                    <h2><?php echo $approved_txn; ?></h2>
                    <small>Ready for claiming/printing</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h6 class="text-muted">Blotter Reports</h6>
                    <h2><?php echo $my_blotter; ?></h2>
                    <small><a href="blotter.php">View my cases</a></small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent requests -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <strong><i class="bi bi-clock-history"></i> Recent Requests</strong>
                </div>
                <div class="card-body">
                    <?php if ($req->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Requested</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($r = $req->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($r['transaction_type']); ?></td>
                                        <td>
                                            <?php
                                                $s = $r['status'];
                                                $badge = $s === 'approved' ? 'success' :
                                                         ($s === 'rejected' ? 'danger' :
                                                         ($s === 'processing' ? 'info' : 'warning'));
                                            ?>
                                            <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($s); ?></span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($r['requested_date'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No requests yet. <a href="transactions.php">Submit your first request</a>.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <strong><i class="bi bi-megaphone"></i> Latest Announcements</strong>
                </div>
                <div class="card-body">
                    <?php if ($ann->num_rows > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php while ($a = $ann->fetch_assoc()): ?>
                                <div class="list-group-item">
                                    <strong><?php echo htmlspecialchars($a['title']); ?></strong>
                                    <div class="small text-muted">
                                        <?php echo date('M d, Y', strtotime($a['posted_date'])); ?>
                                    </div>
                                    <p class="mb-1 mt-1">
                                        <?php echo nl2br(htmlspecialchars(substr($a['content'], 0, 120))); ?>…
                                    </p>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">No announcements at this time.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
