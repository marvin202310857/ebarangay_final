<?php
// register.php
require_once 'config/config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $middle_name = $conn->real_escape_string($_POST['middle_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $address = $conn->real_escape_string($_POST['address']);
    
    // Check if username exists
    $check = $conn->query("SELECT user_id FROM users WHERE username = '$username' OR email = '$email'");
    
    if ($check->num_rows > 0) {
        $error = 'Username or email already exists.';
    } else {
        $conn->begin_transaction();
        
        try {
            // Insert user
            $sql1 = "INSERT INTO users (username, password, email, user_type, status) 
                     VALUES ('$username', '$password', '$email', 'resident', 'pending')";
            $conn->query($sql1);
            $user_id = $conn->insert_id;
            
            // Insert resident profile
            $sql2 = "INSERT INTO residents (user_id, first_name, middle_name, last_name, birthdate, gender, contact_number, address) 
                     VALUES ($user_id, '$first_name', '$middle_name', '$last_name', '$birthdate', '$gender', '$contact_number', '$address')";
            $conn->query($sql2);
            
            $conn->commit();
            $success = 'Registration successful! Please wait for admin approval before logging in.';
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - eBarangay.ph</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 0;
        }
        .register-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="card">
                <div class="card-header bg-white text-center p-4">
                    <h3 class="text-primary">Resident Registration</h3>
                    <p class="text-muted mb-0">Create your eBarangay.ph account</p>
                </div>
                <div class="card-body p-4">
                    <?php if ($success): ?>
                        <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> <?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <h5 class="mb-3">Account Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Username *</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password *</label>
                                <input type="password" class="form-control" name="password" required minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" name="confirm_password" required minlength="6">
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <h5 class="mb-3">Personal Information</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" name="first_name" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="middle_name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control" name="last_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Birthdate *</label>
                                <input type="date" class="form-control" name="birthdate" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender *</label>
                                <select class="form-control" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Number *</label>
                                <input type="text" class="form-control" name="contact_number" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Complete Address *</label>
                                <textarea class="form-control" name="address" rows="2" required></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-person-plus"></i> Register
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="mb-0">Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
