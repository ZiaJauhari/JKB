<?php
$page_title = "Daftar";
require_once 'includes/header.php';

// Get database connection
$db = getDB();

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = sanitize($_POST['full_name'] ?? '');

    $errors = [];

    if (!$username) $errors[] = 'Username wajib diisi.';
    if (!$email) $errors[] = 'Email wajib diisi.';
    if (!$password) $errors[] = 'Password wajib diisi.';
    if (!$full_name) $errors[] = 'Nama lengkap wajib diisi.';
    if ($password !== $confirm_password) $errors[] = 'Konfirmasi password tidak cocok.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    if (!validateEmail($email)) $errors[] = 'Format email tidak valid.';

    // Check if username or email already exists
    if ($username) {
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) $errors[] = 'Username sudah digunakan.';
    }

    if ($email) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = 'Email sudah terdaftar.';
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password, full_name, email) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $hashed_password, $full_name, $email])) {
            setFlashMessage('success', 'Pendaftaran berhasil! Silakan login.');
            redirect('login.php');
        } else {
            setFlashMessage('danger', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.');
        }
    } else {
        $error_message = implode('<br>', $errors);
        setFlashMessage('danger', $error_message);
    }
}

// Check if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}
?>

<!-- Register Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold">Daftar Akun</h2>
                            <p class="text-muted">Buat akun baru untuk mengakses fitur lengkap</p>
                        </div>

                        <?php
                        $flash = getFlashMessage();
                        if ($flash): ?>
                        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                            <?php echo $flash['message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="register" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Daftar
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-2">Sudah punya akun?</p>
                            <a href="login.php" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="index.php" class="btn btn-link">
                                <i class="fas fa-home me-2"></i>Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
