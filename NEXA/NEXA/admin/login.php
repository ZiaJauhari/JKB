<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi';
    } else {
        try {
            $db = getDB();

            // Check if username exists in admin_users
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
            $stmt->execute(array($username));
            $admin_user = $stmt->fetch();

            if ($admin_user) {
                // Username found in admin_users, check password
                if (password_verify($password, $admin_user['password'])) {
                    // Set session for admin
                    $_SESSION['admin_id'] = $admin_user['id'];
                    $_SESSION['admin_username'] = $admin_user['username'];
                    $_SESSION['admin_name'] = $admin_user['full_name'];
                    $_SESSION['admin_role'] = $admin_user['role'];

                    // Update last login
                    $stmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    $stmt->execute(array($admin_user['id']));

                    redirect('dashboard.php');
                } else {
                    $error = 'Password salah';
                }
            } else {
                // Check if username exists in users
                $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
                $stmt->execute(array($username));
                $regular_user = $stmt->fetch();

                if ($regular_user) {
                    // Username found in users, check password
                    if (password_verify($password, $regular_user['password'])) {
                        // Set session for regular user (optional, since no user dashboard)
                        $_SESSION['user_id'] = $regular_user['id'];
                        $_SESSION['user_username'] = $regular_user['username'];
                        $_SESSION['user_name'] = $regular_user['full_name'];

                        // Update last login
                        $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                        $stmt->execute(array($regular_user['id']));

                        redirect('../index.php');
                    } else {
                        $error = 'Password salah';
                    }
                } else {
                    // Username not registered, redirect to sign up
                    redirect('signup.php');
                }
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nexa Trade</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card mx-auto">
            <div class="login-header">
                <i class="fas fa-globe-asia fa-3x mb-3"></i>
                <h3 class="mb-0">Nexa Trade</h3>
                <p class="mb-0 small">(Nusantara Export Asia)</p>
                <p class="mb-0">Login</p>
            </div>
            
            <div class="login-body">
                <h4 class="text-center mb-4">Login</h4>
                
                <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user me-2"></i>Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Masukkan username" required autofocus
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Masukkan password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
                
                <hr class="my-4">

                <div class="text-center">
                    <p class="mb-2">Belum punya akun?</p>
                    <a href="signup.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-user-plus me-2"></i>Sign Up
                    </a>
                    <a href="../index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Website
                    </a>
                </div>
                
                <div class="alert alert-info mt-3 mb-0" role="alert">
                    <small>
                        <strong>Demo Login:</strong><br>
                        Username: <code>admin</code><br>
                        Password: <code>admin123</code>
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
