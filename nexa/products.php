<?php
$page_title = "Produk Ekspor";
require_once 'includes/header.php';

// Get database connection
$db = getDB();

// Get filter parameters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'newest';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 9;

// Build query
$where = ["is_active = 1"];
$params = [];

if ($category && $category !== 'all') {
    $where[] = "category = :category";
    $params[':category'] = $category;
}

if ($search) {
    $where[] = "(product_name LIKE :search OR description LIKE :search)";
    $params[':search'] = "%$search%";
}

$where_clause = implode(' AND ', $where);

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM products WHERE $where_clause";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetch()['total'];

// Calculate pagination
$pagination = paginate($total_items, $items_per_page, $page);

// Determine sort order
switch($sort) {
    case 'price_low':
        $order_by = 'price ASC';
        break;
    case 'price_high':
        $order_by = 'price DESC';
        break;
    case 'name':
        $order_by = 'product_name ASC';
        break;
    default:
        $order_by = 'created_at DESC';
}

// Get products
$sql = "SELECT * FROM products WHERE $where_clause ORDER BY $order_by LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Get categories with count
$categories_sql = "SELECT category, COUNT(*) as count FROM products WHERE is_active = 1 GROUP BY category";
$categories = $db->query($categories_sql)->fetchAll();
?>

<!-- Page Header -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Katalog Produk Ekspor</h1>
                <p class="lead text-muted mb-0">
                    Temukan produk berkualitas Indonesia untuk pasar global
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-lg-end mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                        <li class="breadcrumb-item active">Produk</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-filter me-2"></i>Filter Produk
                        </h5>
                        
                        <!-- Search -->
                        <form method="GET" action="" class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Cari produk..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <?php if($category): ?>
                            <input type="hidden" name="category" value="<?php echo $category; ?>">
                            <?php endif; ?>
                        </form>
                        
                        <!-- Categories -->
                        <h6 class="mb-3">Kategori</h6>
                        <div class="list-group mb-4">
                            <a href="products.php" 
                               class="list-group-item list-group-item-action <?php echo !$category ? 'active' : ''; ?>">
                                <i class="fas fa-th me-2"></i>Semua Produk
                                <span class="badge bg-primary float-end"><?php echo $total_items; ?></span>
                            </a>
                            <?php foreach($categories as $cat): ?>
                            <a href="products.php?category=<?php echo $cat['category']; ?>" 
                               class="list-group-item list-group-item-action <?php echo $category == $cat['category'] ? 'active' : ''; ?>">
                                <i class="fas fa-<?php 
                                    $icon = 'box';
                                    if($cat['category'] == 'pertanian') $icon = 'leaf';
                                    elseif($cat['category'] == 'kerajinan') $icon = 'palette';
                                    elseif($cat['category'] == 'perikanan') $icon = 'fish';
                                    echo $icon;
                                ?> me-2"></i>
                                <?php echo ucfirst($cat['category']); ?>
                                <span class="badge bg-primary float-end"><?php echo $cat['count']; ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Sort -->
                        <h6 class="mb-3">Urutkan</h6>
                        <form method="GET" action="">
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Terbaru</option>
                                <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Harga Terendah</option>
                                <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Harga Tertinggi</option>
                                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Nama A-Z</option>
                            </select>
                            <?php if($category): ?>
                            <input type="hidden" name="category" value="<?php echo $category; ?>">
                            <?php endif; ?>
                            <?php if($search): ?>
                            <input type="hidden" name="search" value="<?php echo $search; ?>">
                            <?php endif; ?>
                        </form>
                        
                        <!-- Clear Filters -->
                        <?php if($category || $search): ?>
                        <div class="mt-3">
                            <a href="products.php" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fas fa-times me-2"></i>Hapus Filter
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Contact Card -->
                <div class="card shadow-sm mt-4 bg-gradient-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-headset fa-3x mb-3"></i>
                        <h5 class="card-title">Butuh Bantuan?</h5>
                        <p class="card-text">Tim kami siap membantu Anda</p>
                        <a href="contact.php" class="btn btn-light">
                            <i class="fas fa-envelope me-2"></i>Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Results Info -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-0">
                            Menampilkan <?php echo count($products); ?> dari <?php echo $total_items; ?> produk
                        </h5>
                        <?php if($search): ?>
                        <small class="text-muted">Hasil pencarian untuk: "<?php echo htmlspecialchars($search); ?>"</small>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if(count($products) > 0): ?>
                <!-- Products Grid -->
                <div class="row">
                    <?php foreach($products as $product): ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
                        <div class="card product-card h-100">
                            <?php if($product['is_featured']): ?>
                            <span class="badge bg-primary product-badge">Featured</span>
                            <?php elseif($product['stock_status'] == 'limited'): ?>
                            <span class="badge bg-warning product-badge">Stok Terbatas</span>
                            <?php elseif($product['stock_status'] == 'out_of_stock'): ?>
                            <span class="badge bg-danger product-badge">Habis</span>
                            <?php endif; ?>
                            
                            <img src="<?php echo $product['image_url']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            
                            <div class="card-body">
                                <span class="badge bg-<?php echo getCategoryBadge($product['category']); ?> mb-2">
                                    <?php echo ucfirst($product['category']); ?>
                                </span>
                                <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                <p class="card-text text-muted">
                                    <?php echo getExcerpt($product['description'], 100); ?>
                                </p>
                                
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Harga</small>
                                        <small class="text-muted">MOQ: <?php echo $product['min_order']; ?> <?php echo $product['unit']; ?></small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                                            <small class="product-unit">per <?php echo $product['unit']; ?></small>
                                        </div>
                                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-info-circle me-1"></i>Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if($pagination['total_pages'] > 1): ?>
                <nav aria-label="Product pagination">
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . $search : ''; ?><?php echo $sort ? '&sort=' . $sort : ''; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <!-- Page Numbers -->
                        <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if($i == 1 || $i == $pagination['total_pages'] || abs($i - $page) <= 2): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . $search : ''; ?><?php echo $sort ? '&sort=' . $sort : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php elseif(abs($i - $page) == 3): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <!-- Next -->
                        <li class="page-item <?php echo $page >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . $search : ''; ?><?php echo $sort ? '&sort=' . $sort : ''; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                <!-- No Results -->
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-5x text-muted mb-4"></i>
                    <h3>Produk Tidak Ditemukan</h3>
                    <p class="text-muted mb-4">Maaf, tidak ada produk yang sesuai dengan pencarian Anda.</p>
                    <a href="products.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Semua Produk
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
