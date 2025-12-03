<?php
// admin/pets.php
require_once '../config/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $status = $_GET['action'] === 'approve' ? 'approved' :
              ($_GET['action'] === 'reject' ? 'rejected' : null);

    if ($status) {
        $conn->query("UPDATE pet_registrations SET status='$status' WHERE pet_id=$id");
    }
    redirect('pets.php');
}

$sql = "SELECT p.*, r.first_name, r.last_name
        FROM pet_registrations p
        JOIN residents r ON p.resident_id = r.resident_id
        ORDER BY p.registration_date DESC";
$pets = $conn->query($sql);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-paw"></i> Pet Registrations</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if ($pets->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Resident</th>
                                <th>Pet name</th>
                                <th>Type</th>
                                <th>Breed</th>
                                <th>Color</th>
                                <th>Age</th>
                                <th>Vaccination</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i=1; while ($row = $pets->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['pet_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['breed']); ?></td>
                                <td><?php echo htmlspecialchars($row['color']); ?></td>
                                <td><?php echo (int) $row['age']; ?></td>
                                <td><?php echo htmlspecialchars($row['vaccination_status']); ?></td>
                                <td>
                                    <?php
                                        $status = $row['status'];
                                        $badge = $status === 'approved' ? 'success' :
                                                 ($status === 'rejected' ? 'danger' : 'warning');
                                    ?>
                                    <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($row['registration_date'])); ?></td>
                                <td>
                                    <?php if ($status === 'pending'): ?>
                                        <a href="pets.php?action=approve&id=<?php echo $row['pet_id']; ?>"
                                           class="btn btn-sm btn-success mb-1">Approve</a>
                                        <a href="pets.php?action=reject&id=<?php echo $row['pet_id']; ?>"
                                           class="btn btn-sm btn-danger mb-1">Reject</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No pet registrations found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
