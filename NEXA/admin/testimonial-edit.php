<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Edit Testimoni";
$errors = array();

if (!isset($_GET['id'])) {
    setFlashMessage('danger', 'ID testimoni tidak ditemukan!');
    redirect('testimonials.php');
}

$id = (int)$_GET['id'];
$stmt = $db->prepare("SELECT * FROM testimonials WHERE id = ?");
$stmt->execute(array($id));
$testimonial = $stmt->fetch();

if (!$testimonial) {
    setFlashMessage('danger', 'Testimoni tidak ditemukan!');
    redirect('testimonials.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = sanitize($_POST['client_name']);
    $company_name = sanitize($_POST['company_name']);
    $position = sanitize($_POST['position']);
    $testimonial_text = sanitize($_POST['testimonial']);
    $rating = intval($_POST['rating']);
    $image_url = sanitize($_POST['image_url']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($client_name)) $errors[] = "Nama klien harus diisi";
    if (empty($testimonial_text)) $errors[] = "Testimoni harus diisi";
    if ($rating < 1 || $rating > 5) $errors[] = "Rating harus antara 1-5";
    
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE testimonials SET client_name = ?, company_name = ?, position = ?, testimonial = ?, rating = ?, image_url = ?, is_featured = ?, is_active = ? WHERE id = ?");
            $stmt->execute(array($client_name, $company_name, $position, $testimonial_text, $rating, $image_url, $is_featured, $is_active, $id));
            setFlashMessage('success', 'Testimoni berhasil diupdate!');
            redirect('testimonials.php');
        } catch (Exception $e) {
            $errors[] = "Gagal mengupdate testimoni: " . $e->getMessage();
        }
    }
} else {
    $_POST = $testimonial;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit me-2"></i>Edit Testimoni</h2>
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
                                    <input type="text" class="form-control" name="client_name" required value="<?php echo htmlspecialchars($_POST['client_name']); ?>">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Perusahaan</label>
                                        <input type="text" class="form-control" name="company_name" value="<?php echo htmlspecialchars($_POST['company_name']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Posisi/Jabatan</label>
                                        <input type="text" class="form-control" name="position" value="<?php echo htmlspecialchars($_POST['position']); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Testimoni <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="testimonial" rows="5" required><?php echo htmlspecialchars($_POST['testimonial']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">URL Foto</label>
                                    <input type="url" class="form-control" name="image_url" value="<?php echo htmlspecialchars($_POST['image_url']); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Rating <span class="text-danger">*</span></label>
                                    <select class="form-select" name="rating" required>
                                        <option value="5" <?php echo $_POST['rating'] == 5 ? 'selected' : ''; ?>>⭐⭐⭐⭐⭐ (5)</option>
                                        <option value="4" <?php echo $_POST['rating'] == 4 ? 'selected' : ''; ?>>⭐⭐⭐⭐ (4)</option>
                                        <option value="3" <?php echo $_POST['rating'] == 3 ? 'selected' : ''; ?>>⭐⭐⭐ (3)</option>
                                        <option value="2" <?php echo $_POST['rating'] == 2 ? 'selected' : ''; ?>>⭐⭐ (2)</option>
                                        <option value="1" <?php echo $_POST['rating'] == 1 ? 'selected' : ''; ?>>⭐ (1)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" <?php echo $_POST['is_featured'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Testimoni Unggulan</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" <?php echo $_POST['is_active'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Aktif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="testimonials.php" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
