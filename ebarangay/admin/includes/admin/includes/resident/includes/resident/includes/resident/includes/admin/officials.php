<?php
// admin/officials.php
require_once '../config/config.php';
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $position = $conn->real_escape_string($_POST['position']);
    $term_start = $conn->real_escape_string($_POST['term_start']);
    $term_end = $conn->real_escape_string($_POST['term_end']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);

    if (!empty($_POST['official_id'])) {
        $id = (int) $_POST['official_id'];
        $sql = "UPDATE barangay_officials
                SET name='$name', position='$position', term_start='$term_start',
                    term_end='$term_end', contact_number='$contact_number'
                WHERE official_id=$id";
    } else {
        $sql = "INSERT INTO barangay_officials
                (name, position, term_start, term_end, contact_number)
                VALUES ('$name','$position','$term_start','$term_end','$contact_number')";
    }

    if ($conn->query($sql)) {
        $message = 'Official saved.';
    } else {
        $message = 'Error saving official.';
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM barangay_officials WHERE official_id=$id");
    redirect('officials.php');
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $res = $conn->query("SELECT * FROM barangay_officials WHERE official_id=$id");
    $edit_data = $res->fetch_assoc();
}

$officials = $conn->query("SELECT * FROM barangay_officials ORDER BY position ASC");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-person-badge"></i> Barangay Officials</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><strong><?php echo $edit_data ? 'Edit' : 'New'; ?> official</strong></div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="official_id" value="<?php echo $edit_data['official_id'] ?? ''; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required
                               value="<?php echo htmlspecialchars($edit_data['name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Position *</label>
                        <input type="text" name="position" class="form-control" required
                               value="<?php echo htmlspecialchars($edit_data['position'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Term start</label>
                        <input type="date" name="term_start" class="form-control"
                               value="<?php echo htmlspecialchars($edit_data['term_start'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Term end</label>
                        <input type="date" name="term_end" class="form-control"
                               value="<?php echo htmlspecialchars($edit_data['term_end'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact number</label>
                        <input type="text" name="contact_number" class="form-control"
                               value="<?php echo htmlspecialchars($edit_data['contact_number'] ?? ''); ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save
                </button>
                <?php if ($edit_data): ?>
                    <a href="officials.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <h5 class="mb-3">All officials</h5>
    <div class="card">
        <div class="card-body">
            <?php if ($officials->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Term</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i=1; while ($row = $officials->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['position']); ?></td>
                                <td>
                                    <?php
                                        echo $row['term_start'] ? date('Y-m-d', strtotime($row['term_start'])) : '';
                                        echo $row['term_end'] ? ' - '.date('Y-m-d', strtotime($row['term_end'])) : '';
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                <td>
                                    <a href="officials.php?edit=<?php echo $row['official_id']; ?>"
                                       class="btn btn-sm btn-primary">Edit</a>
                                    <a href="officials.php?delete=<?php echo $row['official_id']; ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this official?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No officials recorded.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
