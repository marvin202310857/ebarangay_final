<?php
// admin/household.php
require_once '../config/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$message = '';

// Create household table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS households (
    household_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    household_number VARCHAR(50) UNIQUE NOT NULL,
    head_resident_id INT(11),
    address TEXT,
    purok VARCHAR(50),
    total_members INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (head_resident_id) REFERENCES residents(resident_id)
)");

$conn->query("CREATE TABLE IF NOT EXISTS household_members (
    member_id INT(11) PRIMARY KEY AUTO_INCREMENT,
    household_id INT(11),
    resident_id INT(11),
    relationship VARCHAR(50),
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (household_id) REFERENCES households(household_id),
    FOREIGN KEY (resident_id) REFERENCES residents(resident_id)
)");

// Add new household
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_household'])) {
    $household_number = $conn->real_escape_string($_POST['household_number']);
    $head_resident_id = (int) $_POST['head_resident_id'];
    $address = $conn->real_escape_string($_POST['address']);
    $purok = $conn->real_escape_string($_POST['purok']);
    
    $sql = "INSERT INTO households (household_number, head_resident_id, address, purok)
            VALUES ('$household_number', $head_resident_id, '$address', '$purok')";
    
    if ($conn->query($sql)) {
        $message = 'Household added successfully.';
    } else {
        $message = 'Error adding household.';
    }
}

// Get all households
$households = $conn->query("SELECT h.*, r.first_name, r.last_name,
                           (SELECT COUNT(*) FROM household_members WHERE household_id = h.household_id) as member_count
                           FROM households h
                           LEFT JOIN residents r ON h.head_resident_id = r.resident_id
                           ORDER BY h.household_number");

$residents = $conn->query("SELECT resident_id, first_name, middle_name, last_name FROM residents ORDER BY last_name");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-house-door"></i> Household Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHouseholdModal">
            <i class="bi bi-plus-circle"></i> Add Household
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6>Total Households</h6>
                    <h2><?php echo $households->num_rows; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6>Total Members</h6>
                    <h2><?php 
                        $total_members = $conn->query("SELECT SUM(member_count) as total FROM 
                            (SELECT household_id, COUNT(*) as member_count FROM household_members GROUP BY household_id) as counts")
                            ->fetch_assoc()['total'] ?? 0;
                        echo $total_members;
                    ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Household #</th>
                            <th>Head of Family</th>
                            <th>Address</th>
                            <th>Purok</th>
                            <th>Members</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($households->num_rows > 0): ?>
                        <?php while ($row = $households->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['household_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td><?php echo htmlspecialchars($row['purok']); ?></td>
                            <td><span class="badge bg-info"><?php echo $row['member_count']; ?> members</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewHouseholdModal<?php echo $row['household_id']; ?>">
                                    <i class="bi bi-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No households registered.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add Household Modal -->
<div class="modal fade" id="addHouseholdModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-house-door"></i> Add New Household</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Household Number *</label>
                        <input type="text" name="household_number" class="form-control" required placeholder="e.g., HH-2025-001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Head of Household *</label>
                        <select name="head_resident_id" class="form-control" required>
                            <option value="">Select resident...</option>
                            <?php 
                            $residents->data_seek(0);
                            while ($res = $residents->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $res['resident_id']; ?>">
                                    <?php echo htmlspecialchars($res['first_name'] . ' ' . $res['last_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address *</label>
                        <textarea name="address" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purok</label>
                        <input type="text" name="purok" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_household" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Household
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
