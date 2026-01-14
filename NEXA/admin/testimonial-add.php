<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Tambah Testimoni";
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = sanitize($_POST['client_name']);
    $company_name = sanitize($_POST['company_name']);
    $position = sanitize($_POST['position']);
    $testimonial = sanitize($_POST['testimonial']);
    $rating = intval($_POST['rating']);
    $image_url = sanitize($_POST['image_url']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($client_name)) $errors[] = "Nama klien harus diisi";
    if (empty($testimonial)) $errors[] = "Testimoni harus diisi";
    if ($rating < 1 || $rating > 5) $errors[] = "Rating harus antara 1-5";
    
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO testimonials (client_name, company_name, position, testimonial, rating, image_url, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array($client_name, $company_name, $position, $testimonial, $rating, $image_url, $is_featured, $is_active));
            setFlashMessage('success', 'Testimoni berhasil ditambahkan!');
            redirect('testimonials.php');
        } catch (Exception $e) {
            $errors[] = "Gagal menambahkan testimoni: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-plus-circle me-2"></i>Tambah Testimoni</h2>
                <a href="testimonials.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
            </div>
            
            <?php if (count($errors) > 0): ?>
            <div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Nama Klien <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="client_name" required value="<?php echo isset($_POST['client_name']) ? htmlspecialchars($_POST['client_name']) : ''; ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Perusahaan</label>
                                        <input type="text" class="form-control" name="company_name" value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Posisi/Jabatan</label>
                                        <input type="text" class="form-control" name="position" value="<?php echo isset($_POST['position']) ? htmlspecialchars($_POST['position']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Testimoni <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="testimonial" rows="5" required><?php echo isset($_POST['testimonial']) ? htmlspecialchars($_POST['testimonial']) : ''; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">URL Foto</label>
                                    <input type="url" class="form-control" name="image_url" value="<?php echo isset($_POST['image_url']) ? htmlspecialchars($_POST['image_url']) : ''; ?>">
                                    <small class="text-muted">Kosongkan untuk menggunakan avatar default</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Rating <span class="text-danger">*</span></label>
                                    <select class="form-select" name="rating" required>
                                        <option value="5" selected>⭐⭐⭐⭐⭐ (5)</option>
                                        <option value="4">⭐⭐⭐⭐ (4)</option>
                                        <option value="3">⭐⭐⭐ (3)</option>
                                        <option value="2">⭐⭐ (2)</option>
                                        <option value="1">⭐ (1)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_featured" value="1">
                                        <label class="form-check-label">Testimoni Unggulan</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                        <label class="form-check-label">Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="testimonials.php" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
