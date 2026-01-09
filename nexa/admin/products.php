<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Kelola Produk";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute(array($id));
        setFlashMessage('success', 'Produk berhasil dihapus!');
        redirect('products.php');
    } catch (Exception $e) {
        setFlashMessage('danger', 'Gagal menghapus produk!');
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
    $where[] = "(product_name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category) {
    $where[] = "category = ?";
    $params[] = $category;
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM products $where_clause";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetch()['total'];

// Calculate pagination
$pagination = paginate($total_items, $items_per_page, $page);

// Get products
$sql = "SELECT * FROM products $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
$execute_params = array_merge($params, array($items_per_page, $pagination['offset']));
$stmt->execute($execute_params);
$products = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-box me-2"></i>Kelola Produk</h2>
                    <p class="text-muted mb-0">Tambah, edit, dan hapus produk ekspor</p>
                </div>
                <a href="product-add.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Produk
                </a>
            </div>
            
            <!-- Filter & Search -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari produk..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                <option value="pertanian" <?php echo $category == 'pertanian' ? 'selected' : ''; ?>>Pertanian</option>
                                <option value="kerajinan" <?php echo $category == 'kerajinan' ? 'selected' : ''; ?>>Kerajinan</option>
                                <option value="perikanan" <?php echo $category == 'perikanan' ? 'selected' : ''; ?>>Perikanan</option>
                                <option value="lainnya" <?php echo $category == 'lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="products.php" class="btn btn-secondary w-100">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>MOQ</th>
                                    <th>Status</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($products) > 0): ?>
                                    <?php foreach($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $product['image_url']; ?>" 
                                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                     class="rounded me-2"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                                                    <?php if($product['is_featured']): ?>
                                                    <span class="badge bg-warning ms-2">Featured</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo getCategoryBadge($product['category']); ?>">
                                                <?php echo ucfirst($product['category']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatPrice($product['price']); ?>/<?php echo $product['unit']; ?></td>
                                        <td><?php echo $product['min_order']; ?> <?php echo $product['unit']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="product-edit.php?id=<?php echo $product['id']; ?>" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="products.php?delete=<?php echo $product['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                               title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Tidak ada produk ditemukan</p>
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
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?>">
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
