<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Edit Artikel";
$errors = array();

if (!isset($_GET['id'])) {
    setFlashMessage('danger', 'ID artikel tidak ditemukan!');
    redirect('articles.php');
}

$id = (int)$_GET['id'];
$stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute(array($id));
$article = $stmt->fetch();

if (!$article) {
    setFlashMessage('danger', 'Artikel tidak ditemukan!');
    redirect('articles.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $slug = generateSlug($title);
    $category = sanitize($_POST['category']);
    $excerpt = sanitize($_POST['excerpt']);
    $content = $_POST['content'];
    $image_url = sanitize($_POST['image_url']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $published_at = $is_published && !$article['is_published'] ? date('Y-m-d H:i:s') : $article['published_at'];
    
    if (empty($title)) $errors[] = "Judul artikel harus diisi";
    if (empty($content)) $errors[] = "Konten artikel harus diisi";
    
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE articles SET title = ?, slug = ?, category = ?, excerpt = ?, content = ?, image_url = ?, is_published = ?, published_at = ? WHERE id = ?");
            $stmt->execute(array($title, $slug, $category, $excerpt, $content, $image_url, $is_published, $published_at, $id));
            setFlashMessage('success', 'Artikel berhasil diupdate!');
            redirect('articles.php');
        } catch (Exception $e) {
            $errors[] = "Gagal mengupdate artikel: " . $e->getMessage();
        }
    }
} else {
    $_POST = $article;
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit me-2"></i>Edit Artikel</h2>
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
                                    <input type="text" class="form-control" name="title" required value="<?php echo htmlspecialchars($_POST['title']); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Excerpt/Ringkasan</label>
                                    <textarea class="form-control" name="excerpt" rows="3"><?php echo htmlspecialchars($_POST['excerpt']); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Konten Artikel <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="content" rows="15" required><?php echo htmlspecialchars($_POST['content']); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">URL Gambar</label>
                                    <input type="url" class="form-control" name="image_url" value="<?php echo htmlspecialchars($_POST['image_url']); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select class="form-select" name="category">
                                        <option value="tips" <?php echo $_POST['category'] == 'tips' ? 'selected' : ''; ?>>Tips Ekspor</option>
                                        <option value="news" <?php echo $_POST['category'] == 'news' ? 'selected' : ''; ?>>Berita</option>
                                        <option value="guide" <?php echo $_POST['category'] == 'guide' ? 'selected' : ''; ?>>Panduan</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_published" value="1" <?php echo $_POST['is_published'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Publish Artikel</label>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <small>
                                        <strong>Info:</strong><br>
                                        Views: <?php echo $_POST['views']; ?><br>
                                        Created: <?php echo formatDate($_POST['created_at']); ?><br>
                                        <?php if($_POST['published_at']): ?>
                                        Published: <?php echo formatDate($_POST['published_at']); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="articles.php" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Artikel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
