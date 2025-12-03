<?php
// admin/residents.php
require_once '../config/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$message = '';

// Add new resident
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_resident'])) {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $middle_name = $conn->real_escape_string($_POST['middle_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $birthdate = $conn->real_escape_string($_POST['birthdate']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $civil_status = $conn->real_escape_string($_POST['civil_status']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $purok = $conn->real_escape_string($_POST['purok']);
    
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $conn->begin_transaction();
    try {
        $sql1 = "INSERT INTO users (username, password, email, user_type, status) 
                 VALUES ('$username', '$password', '$email', 'resident', 'active')";
        $conn->query($sql1);
        $user_id = $conn->insert_id;
        
        $sql2 = "INSERT INTO residents (user_id, first_name, middle_name, last_name, birthdate, gender, 
                                       civil_status, contact_number, address, purok) 
                 VALUES ($user_id, '$first_name', '$middle_name', '$last_name', '$birthdate', '$gender',
                        '$civil_status', '$contact_number', '$address', '$purok')";
        $conn->query($sql2);
        
        $conn->commit();
        $message = 'Resident added successfully.';
    } catch (Exception $e) {
        $conn->rollback();
        $message = 'Error adding resident: ' . $e->getMessage();
    }
}

// Get all residents
$residents = $conn->query("SELECT r.*, u.username, u.email, u.status 
                          FROM residents r 
                          JOIN users u ON r.user_id = u.user_id 
                          ORDER BY r.last_name, r.first_name");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-person-vcard"></i> Residents Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResidentModal">
            <i class="bi bi-person-plus"></i> Add New Resident
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Civil Status</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($residents->num_rows > 0): ?>
                        <?php $i = 1; while ($row = $residents->fetch_assoc()): 
                            $age = date_diff(date_create($row['birthdate']), date_create('now'))->y;
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong>
                                <br><small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
                            </td>
                            <td><?php echo $age; ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['civil_status']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Profile">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No residents found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add Resident Modal -->
<div class="modal fade" id="addResidentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Add New Resident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <h6 class="text-primary mb-3">Account Information</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    
                    <h6 class="text-primary mb-3 mt-3">Personal Information</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name *</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name *</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Birthdate *</label>
                            <input type="date" name="birthdate" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Gender *</label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Civil Status</label>
                            <select name="civil_status" class="form-control">
                                <option value="">Select</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control">
                        </div>
                        <div class="col-md-9 mb-3">
                            <label class="form-label">Address *</label>
                            <textarea name="address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Purok</label>
                            <input type="text" name="purok" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_resident" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Resident
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
