<?php
// resident/blotter.php
require_once '../config/config.php';

if (!isLoggedIn() || !isResident()) {
    redirect('../login.php');
}

$resident_id = (int) ($_SESSION['resident_id'] ?? 0);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $incident_type = $conn->real_escape_string($_POST['incident_type']);
    $incident_date = $conn->real_escape_string($_POST['incident_date']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);
    $complainant_name = $conn->real_escape_string($_POST['complainant_name']);
    $respondent_name = $conn->real_escape_string($_POST['respondent_name']);

    $sql = "INSERT INTO blotter_reports 
            (resident_id, incident_type, incident_date, location, description, complainant_name, respondent_name)
            VALUES ($resident_id, '$incident_type', '$incident_date', '$location', '$description',
                    '$complainant_name', '$respondent_name')";
    if ($conn->query($sql)) {
        $message = 'Blotter report submitted successfully.';
    } else {
        $message = 'Error submitting report. Please try again.';
    }
}

$blotters = $conn->query("SELECT * FROM blotter_reports WHERE resident_id = $resident_id ORDER BY reported_date DESC");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-exclamation-triangle"></i> Blotter Reports</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><strong>File new blotter</strong></div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Incident type *</label>
                        <input type="text" name="incident_type" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Incident date & time *</label>
                        <input type="datetime-local" name="incident_date" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Location *</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Complainant name</label>
                        <input type="text" name="complainant_name" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Respondent name</label>
                        <input type="text" name="respondent_name" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-flag"></i> Submit report
                </button>
            </form>
        </div>
    </div>

    <h5 class="mb-3">My blotter reports</h5>
    <div class="card">
        <div class="card-body">
            <?php if ($blotters->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Incident date</th>
                                <th>Reported</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; while ($row = $blotters->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
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
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">You have no blotter reports yet.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
