<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Kelola Testimoni";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute(array($id));
        setFlashMessage('success', 'Testimoni berhasil dihapus!');
        redirect('testimonials.php');
    } catch (Exception $e) {
        setFlashMessage('danger', 'Gagal menghapus testimoni!');
    }
}

// Get filter parameters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;

// Build query
$where = array();
$params = array();

if ($search) {
    $where[] = "(client_name LIKE ? OR company_name LIKE ? OR testimonial LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM testimonials $where_clause";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetch()['total'];

// Calculate pagination
$pagination = paginate($total_items, $items_per_page, $page);

// Get testimonials
$sql = "SELECT * FROM testimonials $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
$execute_params = array_merge($params, array($items_per_page, $pagination['offset']));
$stmt->execute($execute_params);
$testimonials = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-star me-2"></i>Kelola Testimoni</h2>
                    <p class="text-muted mb-0">Tambah, edit, dan hapus testimoni klien</p>
                </div>
                <a href="testimonial-add.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Testimoni
                </a>
            </div>
            
            <!-- Filter & Search -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari testimoni..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="testimonials.php" class="btn btn-secondary w-100">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Testimonials Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Klien</th>
                                    <th>Testimoni</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($testimonials) > 0): ?>
                                    <?php foreach($testimonials as $testimonial): ?>
                                    <tr>
                                        <td><?php echo $testimonial['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $testimonial['image_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($testimonial['client_name']); ?>" 
                                                     alt="<?php echo htmlspecialchars($testimonial['client_name']); ?>"
                                                     class="rounded-circle me-2"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($testimonial['client_name']); ?></strong><br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($testimonial['position']); ?> - 
                                                        <?php echo htmlspecialchars($testimonial['company_name']); ?>
                                                    </small>
                                                    <?php if($testimonial['is_featured']): ?>
                                                    <span class="badge bg-warning ms-2">Featured</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small><?php echo getExcerpt($testimonial['testimonial'], 80); ?></small>
                                        </td>
                                        <td>
                                            <div class="text-warning">
                                                <?php for($i = 0; $i < $testimonial['rating']; $i++): ?>
                                                <i class="fas fa-star"></i>
                                                <?php endfor; ?>
                                                <?php for($i = $testimonial['rating']; $i < 5; $i++): ?>
                                                <i class="far fa-star"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $testimonial['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $testimonial['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="testimonial-edit.php?id=<?php echo $testimonial['id']; ?>" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="testimonials.php?delete=<?php echo $testimonial['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Yakin ingin menghapus testimoni ini?')"
                                               title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Tidak ada testimoni ditemukan</p>
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
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
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
