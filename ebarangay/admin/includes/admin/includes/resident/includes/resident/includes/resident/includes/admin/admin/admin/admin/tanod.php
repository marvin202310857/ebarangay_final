<?php
// admin/tanod.php
require_once '../config/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$message = '';

// Create tanod tables
$conn->query("CREATE TABLE IF NOT EXISTS tanod_personnel (
    tanod_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(200) NOT NULL,
    contact_number VARCHAR(20),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS tanod_patrols (
    patrol_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    tanod_id INT(11),
    patrol_date DATE,
    start_time TIME,
    end_time TIME,
    area_covered TEXT,
    incidents_reported TEXT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tanod_id) REFERENCES tanod_personnel(tanod_id)
)");

// Add tanod personnel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tanod'])) {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    
    $sql = "INSERT INTO tanod_personnel (full_name, contact_number) VALUES ('$full_name', '$contact_number')";
    if ($conn->query($sql)) {
        $message = 'Tanod personnel added successfully.';
    }
}

// Log patrol
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_patrol'])) {
    $tanod_id = (int) $_POST['tanod_id'];
    $patrol_date = $conn->real_escape_string($_POST['patrol_date']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $area_covered = $conn->real_escape_string($_POST['area_covered']);
    $incidents = $conn->real_escape_string($_POST['incidents_reported']);
    $remarks = $conn->real_escape_string($_POST['remarks']);
    
    $sql = "INSERT INTO tanod_patrols (tanod_id, patrol_date, start_time, end_time, area_covered, incidents_reported, remarks)
            VALUES ($tanod_id, '$patrol_date', '$start_time', '$end_time', '$area_covered', '$incidents', '$remarks')";
    
    if ($conn->query($sql)) {
        $message = 'Patrol logged successfully.';
    }
}

$tanod_list = $conn->query("SELECT * FROM tanod_personnel ORDER BY full_name");
$patrols = $conn->query("SELECT p.*, t.full_name FROM tanod_patrols p 
                         JOIN tanod_personnel t ON p.tanod_id = t.tanod_id 
                         ORDER BY p.patrol_date DESC, p.start_time DESC LIMIT 20");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-shield-check"></i> Tanod Tracking System</h1>
        <div>
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addTanodModal">
                <i class="bi bi-person-plus"></i> Add Tanod
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#logPatrolModal">
                <i class="bi bi-journal-plus"></i> Log Patrol
            </button>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">Active Tanod</h6>
                    <h2><?php 
                        echo $conn->query("SELECT COUNT(*) as c FROM tanod_personnel WHERE status='active'")->fetch_assoc()['c'];
                    ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="text-muted">Patrols This Month</h6>
                    <h2><?php 
                        echo $conn->query("SELECT COUNT(*) as c FROM tanod_patrols 
                                          WHERE MONTH(patrol_date) = MONTH(NOW()) 
                                          AND YEAR(patrol_date) = YEAR(NOW())")->fetch_assoc()['c'];
                    ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="text-muted">Incidents Reported</h6>
                    <h2><?php 
                        echo $conn->query("SELECT COUNT(*) as c FROM tanod_patrols 
                                          WHERE incidents_reported IS NOT NULL AND incidents_reported != ''")->fetch_assoc()['c'];
                    ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Recent Patrol Logs</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Tanod</th>
                            <th>Time</th>
                            <th>Area Covered</th>
                            <th>Incidents</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($patrols->num_rows > 0): ?>
                        <?php while ($row = $patrols->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($row['patrol_date'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['full_name']); ?></strong></td>
                            <td><?php echo date('h:i A', strtotime($row['start_time'])) . ' - ' . date('h:i A', strtotime($row['end_time'])); ?></td>
                            <td><?php echo htmlspecialchars($row['area_covered']); ?></td>
                            <td><?php echo $row['incidents_reported'] ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-success">None</span>'; ?></td>
                            <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No patrol logs yet.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add Tanod Modal -->
<div class="modal fade" id="addTanodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Tanod Personnel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_tanod" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Log Patrol Modal -->
<div class="modal fade" id="logPatrolModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Patrol Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Tanod *</label>
                            <select name="tanod_id" class="form-control" required>
                                <option value="">Select tanod...</option>
                                <?php 
                                $tanod_list->data_seek(0);
                                while ($t = $tanod_list->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $t['tanod_id']; ?>"><?php echo htmlspecialchars($t['full_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Patrol Date *</label>
                            <input type="date" name="patrol_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Start Time *</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">End Time *</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Area Covered *</label>
                            <input type="text" name="area_covered" class="form-control" required placeholder="e.g., Purok 1, Purok 2">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Incidents Reported</label>
                            <textarea name="incidents_reported" class="form-control" rows="2" placeholder="Describe any incidents or leave blank if none"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="log_patrol" class="btn btn-primary">Log Patrol</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
