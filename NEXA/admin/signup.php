<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);

    if (empty($username) || empty($password) || empty($confirm_password) || empty($full_name) || empty($email)) {
        $error = 'Semua field harus diisi';
    } elseif (!validateEmail($email)) {
        $error = 'Format email tidak valid';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        try {
            $db = getDB();

            // Check if username or email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ? UNION SELECT id FROM admin_users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email, $username, $email]);
            if ($stmt->fetch()) {
                $error = 'Username atau email sudah terdaftar';
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password, full_name, email) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $full_name, $email]);

                // Get the new user ID
                $user_id = $db->lastInsertId();

                // Auto-login the user
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_username'] = $username;
                $_SESSION['user_name'] = $full_name;

                // Update last login
                $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$user_id]);

                redirect('../index.php');
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
    <title>Sign Up - Nexa Trade</title>

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

        .signup-card {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .signup-header {
            background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .signup-body {
            padding: 2rem;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn-signup {
            background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
        }

        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-card mx-auto">
            <div class="signup-header">
                <i class="fas fa-user-plus fa-3x mb-3"></i>
                <h3 class="mb-0">Nexa Trade</h3>
                <p class="mb-0 small">(Nusantara Export Asia)</p>
                <p class="mb-0">Daftar Akun</p>
            </div>

            <div class="signup-body">
                <h4 class="text-center mb-4">Sign Up</h4>

                <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">
                            <i class="fas fa-user me-2"></i>Nama Lengkap
                        </label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               placeholder="Masukkan nama lengkap" required
                               value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user-circle me-2"></i>Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username"
                               placeholder="Masukkan username" required
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Masukkan email" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Masukkan password" required minlength="6">
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Konfirmasi Password
                        </label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                               placeholder="Konfirmasi password" required minlength="6">
                    </div>

                    <button type="submit" class="btn btn-primary btn-signup w-100">
                        <i class="fas fa-user-plus me-2"></i>Daftar
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center">
                    <p class="mb-2">Sudah punya akun?</p>
                    <a href="login.php" class="btn btn-outline-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                </div>

                <div class="text-center mt-3">
                    <a href="../index.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Website
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
