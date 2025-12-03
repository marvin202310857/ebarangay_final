<?php
// resident/profile.php
require_once '../config/config.php';

if (!isLoggedIn() || !isResident()) {
    redirect('../login.php');
}

$user_id = (int) ($_SESSION['user_id'] ?? 0);

// Get current profile
$sql = "SELECT u.email, r.* 
        FROM users u 
        JOIN residents r ON u.user_id = r.user_id 
        WHERE u.user_id = $user_id";
$result = $conn->query($sql);
$profile = $result->fetch_assoc();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $middle_name = $conn->real_escape_string($_POST['middle_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $birthdate = $conn->real_escape_string($_POST['birthdate']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $civil_status = $conn->real_escape_string($_POST['civil_status']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $purok = $conn->real_escape_string($_POST['purok']);

    $conn->begin_transaction();
    try {
        $conn->query("UPDATE users SET email = '$email' WHERE user_id = $user_id");
        $conn->query("UPDATE residents 
                      SET first_name = '$first_name',
                          middle_name = '$middle_name',
                          last_name = '$last_name',
                          birthdate = '$birthdate',
                          gender = '$gender',
                          civil_status = '$civil_status',
                          contact_number = '$contact_number',
                          address = '$address',
                          purok = '$purok'
                      WHERE user_id = $user_id");
        $conn->commit();
        $message = 'Profile updated successfully.';
        $_SESSION['full_name'] = $first_name . ' ' . $last_name;
    } catch (Exception $e) {
        $conn->rollback();
        $message = 'Error updating profile.';
    }

    $result = $conn->query($sql);
    $profile = $result->fetch_assoc();
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-person-circle"></i> My Profile</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">First name *</label>
                        <input type="text" name="first_name" class="form-control" required
                               value="<?php echo htmlspecialchars($profile['first_name']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Middle name</label>
                        <input type="text" name="middle_name" class="form-control"
                               value="<?php echo htmlspecialchars($profile['middle_name']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Last name *</label>
                        <input type="text" name="last_name" class="form-control" required
                               value="<?php echo htmlspecialchars($profile['last_name']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required
                               value="<?php echo htmlspecialchars($profile['email']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Birthdate *</label>
                        <input type="date" name="birthdate" class="form-control" required
                               value="<?php echo htmlspecialchars($profile['birthdate']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender *</label>
                        <select name="gender" class="form-control" required>
                            <option value="">Select</option>
                            <option value="Male" <?php if ($profile['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if ($profile['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                            <option value="Other" <?php if ($profile['gender'] === 'Other') echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Civil status</label>
                        <select name="civil_status" class="form-control">
                            <option value="">Select</option>
                            <option value="Single" <?php if ($profile['civil_status'] === 'Single') echo 'selected'; ?>>Single</option>
                            <option value="Married" <?php if ($profile['civil_status'] === 'Married') echo 'selected'; ?>>Married</option>
                            <option value="Widowed" <?php if ($profile['civil_status'] === 'Widowed') echo 'selected'; ?>>Widowed</option>
                            <option value="Separated" <?php if ($profile['civil_status'] === 'Separated') echo 'selected'; ?>>Separated</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Contact number</label>
                        <input type="text" name="contact_number" class="form-control"
                               value="<?php echo htmlspecialchars($profile['contact_number']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Purok</label>
                        <input type="text" name="purok" class="form-control"
                               value="<?php echo htmlspecialchars($profile['purok']); ?>">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address *</label>
                        <textarea name="address" class="form-control" rows="2" required><?php 
                            echo htmlspecialchars($profile['address']); 
                        ?></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save changes
                </button>
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
