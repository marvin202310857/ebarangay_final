<?php
// admin/surveillance.php
require_once '../config/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$message = '';

// Create surveillance tables
$conn->query("CREATE TABLE IF NOT EXISTS surveillance_cameras (
    camera_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    camera_name VARCHAR(100) NOT NULL,
    location TEXT,
    ip_address VARCHAR(50),
    status ENUM('online','offline','maintenance') DEFAULT 'online',
    installed_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS surveillance_incidents (
    incident_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    camera_id INT(11),
    incident_date DATETIME,
    incident_type VARCHAR(100),
    description TEXT,
    recorded_by INT(11),
    video_file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (camera_id) REFERENCES surveillance_cameras(camera_id),
    FOREIGN KEY (recorded_by) REFERENCES users(user_id)
)");

// Add camera
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_camera'])) {
    $camera_name = $conn->real_escape_string($_POST['camera_name']);
    $location = $conn->real_escape_string($_POST['location']);
    $ip_address = $conn->real_escape_string($_POST['ip_address']);
    $installed_date = $conn->real_escape_string($_POST['installed_date']);
    
    $sql = "INSERT INTO surveillance_cameras (camera_name, location, ip_address, installed_date)
            VALUES ('$camera_name', '$location', '$ip_address', '$installed_date')";
    
    if ($conn->query($sql)) {
        $message = 'Camera added successfully.';
    }
}

// Log incident
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_incident'])) {
    $camera_id = (int) $_POST['camera_id'];
    $incident_date = $conn->real_escape_string($_POST['incident_date']);
    $incident_type = $conn->real_escape_string($_POST['incident_type']);
    $description = $conn->real_escape_string($_POST['description']);
    $recorded_by = (int) $_SESSION['user_id'];
    
    $sql = "INSERT INTO surveillance_incidents (camera_id, incident_date, incident_type, description, recorded_by)
            VALUES ($camera_id, '$incident_date', '$incident_type', '$description', $recorded_by)";
    
    if ($conn->query($sql)) {
        $message = 'Incident logged successfully.';
    }
}

$cameras = $conn->query("SELECT * FROM surveillance_cameras ORDER BY camera_name");
$incidents = $conn->query("SELECT i.*, c.camera_name, u.username 
                          FROM surveillance_incidents i
                          JOIN surveillance_cameras c ON i.camera_id = c.camera_id
                          JOIN users u ON i.recorded_by = u.user_id
                          ORDER BY i.incident_date DESC LIMIT 20");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-camera-video"></i> Surveillance System</h1>
        <div>
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addCameraModal">
                <i class="bi bi-camera-video"></i> Add Camera
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#logIncidentModal">
                <i class="bi bi-exclamation-triangle"></i> Log Incident
            </button>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Camera Status Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">Online Cameras</h6>
                    <h2 class="text-success"><?php 
                        echo $conn->query("SELECT COUNT(*) as c FROM surveillance_cameras WHERE status='online'")->fetch_assoc()['c'];
                    ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h6 class="text-muted">Offline Cameras</h6>
                    <h2 class="text-danger"><?php 
                        echo $conn->query("SELECT COUNT(*) as c FROM surveillance_cameras WHERE status='offline'")->fetch_assoc()['c'];
                    ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="text-muted">Incidents This Month</h6>
                    <h2 class="text-warning"><?php 
                        echo $conn->query("SELECT COUNT(*) as c FROM surveillance_incidents 
                                          WHERE MONTH(incident_date) = MONTH(NOW()) 
                                          AND YEAR(incident_date) = YEAR(NOW())")->fetch_assoc()['c'];
                    ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="text-muted">Total Cameras</h6>
                    <h2 class="text-info"><?php echo $cameras->num_rows; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Camera List -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong><i class="bi bi-camera"></i> Camera List</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <?php 
                $cameras->data_seek(0);
                while ($cam = $cameras->fetch_assoc()): 
                    $status_color = $cam['status'] === 'online' ? 'success' : ($cam['status'] === 'offline' ? 'danger' : 'warning');
                ?>
                <div class="col-md-4 mb-3">
                    <div class="card border-<?php echo $status_color; ?>">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($cam['camera_name']); ?></h6>
                            <p class="card-text text-muted mb-2">
                                <small><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($cam['location']); ?></small>
                            </p>
                            <p class="card-text mb-2">
                                <small>IP: <?php echo htmlspecialchars($cam['ip_address']); ?></small>
                            </p>
                            <span class="badge bg-<?php echo $status_color; ?>"><?php echo ucfirst($cam['status']); ?></span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                
                <?php if ($cameras->num_rows === 0): ?>
                    <p class="text-center text-muted">No cameras installed yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Incidents -->
    <div class="card">
        <div class="card-header bg-light">
            <strong><i class="bi bi-exclamation-triangle"></i> Recent Incidents</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>Camera</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($incidents->num_rows > 0): ?>
                        <?php while ($inc = $incidents->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y h:i A', strtotime($inc['incident_date'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($inc['camera_name']); ?></strong></td>
                            <td><span class="badge bg-danger"><?php echo htmlspecialchars($inc['incident_type']); ?></span></td>
                            <td><?php echo htmlspecialchars($inc['description']); ?></td>
                            <td><?php echo htmlspecialchars($inc['username']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No incidents recorded.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add Camera Modal -->
<div class="modal fade" id="addCameraModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Surveillance Camera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Camera Name *</label>
                        <input type="text" name="camera_name" class="form-control" required placeholder="e.g., Main Gate Camera">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location *</label>
                        <input type="text" name="location" class="form-control" required placeholder="e.g., Barangay Hall Entrance">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">IP Address</label>
                        <input type="text" name="ip_address" class="form-control" placeholder="192.168.1.100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Installed Date</label>
                        <input type="date" name="installed_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_camera" class="btn btn-success">Add Camera</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Log Incident Modal -->
<div class="modal fade" id="logIncidentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Surveillance Incident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Camera *</label>
                        <select name="camera_id" class="form-control" required>
                            <option value="">Select camera...</option>
                            <?php 
                            $cameras->data_seek(0);
                            while ($cam = $cameras->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $cam['camera_id']; ?>"><?php echo htmlspecialchars($cam['camera_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Incident Date & Time *</label>
                        <input type="datetime-local" name="incident_date" class="form-control" required value="<?php echo date('Y-m-d\TH:i'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Incident Type *</label>
                        <input type="text" name="incident_type" class="form-control" required placeholder="e.g., Suspicious Activity, Vandalism">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="log_incident" class="btn btn-primary">Log Incident</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
