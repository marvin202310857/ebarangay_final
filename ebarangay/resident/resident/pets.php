<?php
// resident/pets.php
require_once '../config/config.php';

if (!isLoggedIn() || !isResident()) {
    redirect('../login.php');
}

$resident_id = (int) ($_SESSION['resident_id'] ?? 0);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pet_name = $conn->real_escape_string($_POST['pet_name']);
    $pet_type = $conn->real_escape_string($_POST['pet_type']);
    $breed = $conn->real_escape_string($_POST['breed']);
    $color = $conn->real_escape_string($_POST['color']);
    $age = (int) $_POST['age'];
    $vaccination_status = $conn->real_escape_string($_POST['vaccination_status']);

    $sql = "INSERT INTO pet_registrations (resident_id, pet_name, pet_type, breed, color, age, vaccination_status)
            VALUES ($resident_id, '$pet_name', '$pet_type', '$breed', '$color', $age, '$vaccination_status')";
    if ($conn->query($sql)) {
        $message = 'Pet registered successfully. Waiting for admin approval.';
    } else {
        $message = 'Error registering pet. Please try again.';
    }
}

$pets = $conn->query("SELECT * FROM pet_registrations WHERE resident_id = $resident_id ORDER BY registration_date DESC");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom d-flex justify-content-between align-items-center">
        <h1 class="h3"><i class="bi bi-paw"></i> My Pets</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <strong>Register New Pet</strong>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pet name *</label>
                        <input type="text" name="pet_name" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pet type *</label>
                        <input type="text" name="pet_type" class="form-control" placeholder="Dog, Cat, etc." required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Breed</label>
                        <input type="text" name="breed" class="form-control">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" class="form-control" min="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vaccination status</label>
                        <input type="text" name="vaccination_status" class="form-control" placeholder="Complete, partial, none">
                    </div>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Submit
                </button>
            </form>
        </div>
    </div>

    <h5 class="mb-3">My registered pets</h5>
    <div class="card">
        <div class="card-body">
            <?php if ($pets->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pet</th>
                                <th>Type</th>
                                <th>Breed</th>
                                <th>Color</th>
                                <th>Age</th>
                                <th>Status</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; while ($row = $pets->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['pet_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['breed']); ?></td>
                                <td><?php echo htmlspecialchars($row['color']); ?></td>
                                <td><?php echo (int) $row['age']; ?></td>
                                <td>
                                    <?php
                                        $status = $row['status'];
                                        $badge = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning');
                                    ?>
                                    <span class="badge bg-<?php echo $badge; ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($row['registration_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">You have no registered pets yet.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
