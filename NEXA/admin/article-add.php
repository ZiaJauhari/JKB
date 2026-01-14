<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Tambah Artikel";
$errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $slug = generateSlug($title);
    $category = sanitize($_POST['category']);
    $excerpt = sanitize($_POST['excerpt']);
    $content = $_POST['content']; // Don't sanitize content to preserve HTML
    $image_url = sanitize($_POST['image_url']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $published_at = $is_published ? date('Y-m-d H:i:s') : null;
    
    if (empty($title)) $errors[] = "Judul artikel harus diisi";
    if (empty($content)) $errors[] = "Konten artikel harus diisi";
    
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO articles (title, slug, category, excerpt, content, image_url, is_published, published_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array($title, $slug, $category, $excerpt, $content, $image_url, $is_published, $published_at));
            setFlashMessage('success', 'Artikel berhasil ditambahkan!');
            redirect('articles.php');
        } catch (Exception $e) {
            $errors[] = "Gagal menambahkan artikel: " . $e->getMessage();
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
                <h2><i class="fas fa-plus-circle me-2"></i>Tambah Artikel</h2>
                <a href="articles.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
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
                                    <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" required value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Excerpt/Ringkasan</label>
                                    <textarea class="form-control" name="excerpt" rows="3"><?php echo isset($_POST['excerpt']) ? htmlspecialchars($_POST['excerpt']) : ''; ?></textarea>
                                    <small class="text-muted">Ringkasan singkat artikel (opsional)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Konten Artikel <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="content" rows="15" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                                    <small class="text-muted">Gunakan HTML untuk formatting</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">URL Gambar</label>
                                    <input type="url" class="form-control" name="image_url" value="<?php echo isset($_POST['image_url']) ? htmlspecialchars($_POST['image_url']) : 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800'; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select class="form-select" name="category">
                                        <option value="tips" <?php echo (isset($_POST['category']) && $_POST['category'] == 'tips') ? 'selected' : 'selected'; ?>>Tips Ekspor</option>
                                        <option value="news" <?php echo (isset($_POST['category']) && $_POST['category'] == 'news') ? 'selected' : ''; ?>>Berita</option>
                                        <option value="guide" <?php echo (isset($_POST['category']) && $_POST['category'] == 'guide') ? 'selected' : ''; ?>>Panduan</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_published" value="1" checked>
                                        <label class="form-check-label">Publish Artikel</label>
                                    </div>
                                    <small class="text-muted">Uncheck untuk menyimpan sebagai draft</small>
                                </div>
                                
                                <div class="alert alert-info">
                                    <strong>Tips:</strong><br>
                                    <small>
                                        - Gunakan judul yang menarik<br>
                                        - Tambahkan gambar berkualitas<br>
                                        - Tulis konten yang informatif<br>
                                        - Gunakan paragraf pendek
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="articles.php" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Artikel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
