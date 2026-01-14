<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Kelola Artikel";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute(array($id));
        setFlashMessage('success', 'Artikel berhasil dihapus!');
        redirect('articles.php');
    } catch (Exception $e) {
        setFlashMessage('danger', 'Gagal menghapus artikel!');
    }
}

// Get filter parameters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;

// Build query
$where = array();
$params = array();

if ($search) {
    $where[] = "(title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $where[] = "category = ?";
    $params[] = $category;
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM articles $where_clause";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetch()['total'];

// Calculate pagination
$pagination = paginate($total_items, $items_per_page, $page);

// Get articles
$sql = "SELECT * FROM articles $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
$execute_params = array_merge($params, array($items_per_page, $pagination['offset']));
$stmt->execute($execute_params);
$articles = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-newspaper me-2"></i>Kelola Artikel</h2>
                    <p class="text-muted mb-0">Tambah, edit, dan hapus artikel blog</p>
                </div>
                <a href="article-add.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Artikel
                </a>
            </div>
            
            <!-- Filter & Search -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="search" placeholder="Cari artikel..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                <option value="tips" <?php echo $category == 'tips' ? 'selected' : ''; ?>>Tips Ekspor</option>
                                <option value="news" <?php echo $category == 'news' ? 'selected' : ''; ?>>Berita</option>
                                <option value="guide" <?php echo $category == 'guide' ? 'selected' : ''; ?>>Panduan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Cari</button>
                        </div>
                        <div class="col-md-2">
                            <a href="articles.php" class="btn btn-secondary w-100"><i class="fas fa-redo me-2"></i>Reset</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Articles Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Artikel</th>
                                    <th>Kategori</th>
                                    <th>Views</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($articles) > 0): ?>
                                    <?php foreach($articles as $article): ?>
                                    <tr>
                                        <td><?php echo $article['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $article['image_url']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="rounded me-2" style="width: 60px; height: 60px; object-fit: cover;">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($article['title']); ?></strong><br>
                                                    <small class="text-muted"><?php echo getExcerpt(strip_tags($article['excerpt']), 60); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-info"><?php echo ucfirst($article['category']); ?></span></td>
                                        <td><i class="far fa-eye me-1"></i><?php echo $article['views']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $article['is_published'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $article['is_published'] ? 'Published' : 'Draft'; ?>
                                            </span>
                                        </td>
                                        <td><small><?php echo formatDate($article['created_at']); ?></small></td>
                                        <td>
                                            <a href="article-edit.php?id=<?php echo $article['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="articles.php?delete=<?php echo $article['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus artikel ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Tidak ada artikel ditemukan</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if($pagination['total_pages'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?>"><i class="fas fa-chevron-left"></i></a>
                            </li>
                            <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $page >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?>"><i class="fas fa-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
