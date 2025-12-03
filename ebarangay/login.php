<?php
// login.php
require_once 'config/config.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('resident/dashboard.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT u.*, r.first_name, r.last_name, r.resident_id 
            FROM users u 
            LEFT JOIN residents r ON u.user_id = r.user_id 
            WHERE u.username = '$username'";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            if ($user['status'] == 'active') {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['resident_id'] = $user['resident_id'];
                $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                if ($user['user_type'] == 'admin') {
                    redirect('admin/dashboard.php');
                } else {
                    redirect('resident/dashboard.php');
                }
            } else if ($user['status'] == 'pending') {
                $error = 'Your account is pending approval. Please wait for admin confirmation.';
            } else {
                $error = 'Your account has been deactivated. Contact the administrator.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - eBarangay.ph</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            max-width: 450px;
            margin: 0 auto;
            animation: fadeInUp 0.5s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .card-header {
            background: white;
            border-bottom: none;
            padding: 2.5rem 2rem 1rem;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-section i {
            font-size: 4.5rem;
            color: #667eea;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .logo-section h3 {
            color: #667eea;
            font-weight: bold;
            margin: 1rem 0 0.5rem;
            font-size: 1.75rem;
        }
        .logo-section p {
            color: #6c757d;
            font-size: 0.95rem;
        }
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.85rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }
        .register-link a {
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header">
                    <div class="logo-section">
                        <i class="bi bi-building"></i>
                        <h3>eBarangay.ph</h3>
                        <p>Digital Barangay Management System</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['logout'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> You have been logged out successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="loginForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-person"></i> Username
                            </label>
                            <input type="text" class="form-control" name="username" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-lock"></i> Password
                            </label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-login w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>
                    
                    <div class="register-link">
                        <p class="mb-0">Don't have an account? 
                            <a href="register.php"><i class="bi bi-person-plus"></i> Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
