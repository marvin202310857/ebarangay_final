<?php
// admin/blotter.php
require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blotter_id'])) {
    $blotter_id = (int) $_POST['blotter_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $admin_response = $conn->real_escape_string($_POST['admin_response']);
    $resolved_by = (int) $_SESSION['user_id'];

    $sql = "UPDATE blotter_reports
            SET status = '$status',
                admin_response = '$admin_response',
                resolved_by = $resolved_by,
                resolved_date = IF('$status' IN ('resolved','closed'), NOW(), resolved_date)
            WHERE blotter_id = $blotter_id";
    $conn->query($sql);
    redirect('blotter.php');
}

$sql = "SELECT b.*, r.first_name, r.last_name 
        FROM blotter_reports b
        JOIN residents r ON b.resident_id = r.resident_id
        ORDER BY b.reported_date DESC";
$blotters = $conn->query($sql);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-journal-medical"></i> Blotter Records</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if ($blotters->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Complainant</th>
                                <th>Resident</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Incident date</th>
                                <th>Reported</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; while ($row = $blotters->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['complainant_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['incident_type']); ?></td>
                                <td>
                                    <?php
                                        $status = $row['status'];
                                        $badge = $status === 'resolved' || $status === 'closed' ? 'success' :
                                                 ($status === 'ongoing' ? 'info' : 'warning');
                                    ?>
                                    <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td><?php echo date('M d, Y h:i A', strtotime($row['incident_date'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['reported_date'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="collapse"
                                            data-bs-target="#update-<?php echo $row['blotter_id']; ?>">
                                        Update
                                    </button>
                                </td>
                            </tr>
                            <tr class="collapse" id="update-<?php echo $row['blotter_id']; ?>">
                                <td colspan="8">
                                    <form method="POST" class="border rounded p-3 bg-light">
                                        <input type="hidden" name="blotter_id" value="<?php echo $row['blotter_id']; ?>">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="new" <?php if ($status === 'new') echo 'selected'; ?>>New</option>
                                                    <option value="ongoing" <?php if ($status === 'ongoing') echo 'selected'; ?>>Ongoing</option>
                                                    <option value="resolved" <?php if ($status === 'resolved') echo 'selected'; ?>>Resolved</option>
                                                    <option value="closed" <?php if ($status === 'closed') echo 'selected'; ?>>Closed</option>
                                                </select>
                                            </div>
                                            <div class="col-md-9 mb-3">
                                                <label class="form-label">Admin notes/response</label>
                                                <textarea name="admin_response" class="form-control" rows="2"><?php
                                                    echo htmlspecialchars($row['admin_response']);
                                                ?></textarea>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="bi bi-save"></i> Save
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No blotter records found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
