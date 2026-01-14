<?php
$page_title = "Detail Produk";
require_once 'includes/header.php';

// Get database connection
$db = getDB();

// Get product ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Produk Tidak Ditemukan</h1>";
    exit();
}

// Get product details
$stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 - Produk Tidak Ditemukan</h1>";
    exit();
}

// Get related products (same category, excluding current)
$stmt = $db->prepare("SELECT * FROM products WHERE category = ? AND id != ? AND is_active = 1 LIMIT 4");
$stmt->execute([$product['category'], $id]);
$related_products = $stmt->fetchAll();

// Handle inquiry form submission
$inquiry_submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
    if (!isLoggedIn()) {
        setFlashMessage('warning', 'Anda harus login untuk mengirim inquiry.');
    } else {
        $full_name = sanitize($_POST['full_name'] ?? '');
        $company_name = sanitize($_POST['company_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? $product['min_order']);
        $message = sanitize($_POST['message'] ?? '');

        if ($full_name && $email && $phone) {
            $stmt = $db->prepare("INSERT INTO inquiries (full_name, company_name, email, phone, product_type, message, status) VALUES (?, ?, ?, ?, ?, ?, 'new')");
            $stmt->execute([$full_name, $company_name, $email, $phone, $product['product_name'], "Inquiry untuk {$product['product_name']}: Quantity: {$quantity} {$product['unit']}\n\n{$message}"]);

            $inquiry_submitted = true;
            setFlashMessage('success', 'Inquiry Anda telah dikirim. Tim kami akan menghubungi Anda segera.');
        } else {
            setFlashMessage('danger', 'Mohon lengkapi semua field yang wajib.');
        }
    }
}
?>

<!-- Page Header -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                <p class="lead text-muted mb-0">
                    Produk ekspor berkualitas dari Indonesia
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-lg-end mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="products.php">Produk</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['product_name']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Product Detail Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4" data-aos="fade-right">
                <div class="product-detail-image">
                    <?php if($product['is_featured']): ?>
                    <span class="badge bg-primary product-badge">Featured</span>
                    <?php elseif($product['stock_status'] == 'limited'): ?>
                    <span class="badge bg-warning product-badge">Stok Terbatas</span>
                    <?php elseif($product['stock_status'] == 'out_of_stock'): ?>
                    <span class="badge bg-danger product-badge">Habis</span>
                    <?php endif; ?>

                    <img src="<?php echo $product['image_url']; ?>"
                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                         class="img-fluid rounded shadow">
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="product-detail-info">
                    <span class="badge bg-<?php echo getCategoryBadge($product['category']); ?> mb-3">
                        <?php echo ucfirst($product['category']); ?>
                    </span>

                    <h2 class="mb-3"><?php echo htmlspecialchars($product['product_name']); ?></h2>

                    <div class="product-price-detail mb-4">
                        <div class="price"><?php echo formatPrice($product['price']); ?></div>
                        <small class="unit">per <?php echo $product['unit']; ?></small>
                        <div class="moq">MOQ: <?php echo $product['min_order']; ?> <?php echo $product['unit']; ?></div>
                    </div>

                    <div class="product-description mb-4">
                        <h5>Deskripsi Produk</h5>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>

                    <div class="product-specs mb-4">
                        <h5>Spesifikasi</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Kategori:</strong> <?php echo ucfirst($product['category']); ?>
                            </div>
                            <div class="col-sm-6">
                                <strong>Satuan:</strong> <?php echo $product['unit']; ?>
                            </div>
                            <div class="col-sm-6">
                                <strong>Status Stok:</strong>
                                <span class="badge bg-<?php echo getStatusBadge($product['stock_status']); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $product['stock_status'])); ?>
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <strong>Minimum Order:</strong> <?php echo $product['min_order']; ?> <?php echo $product['unit']; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Inquiry Button -->
                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#inquiryModal">
                        <i class="fas fa-envelope me-2"></i>Request Inquiry
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products Section -->
<?php if(count($related_products) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Produk Terkait</h2>
            <p class="text-muted">Produk lain dalam kategori yang sama</p>
        </div>

        <div class="row">
            <?php foreach($related_products as $related): ?>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up">
                <div class="card product-card h-100">
                    <?php if($related['stock_status'] == 'limited'): ?>
                    <span class="badge bg-warning product-badge">Stok Terbatas</span>
                    <?php endif; ?>

                    <img src="<?php echo $related['image_url']; ?>"
                         class="card-img-top"
                         alt="<?php echo htmlspecialchars($related['product_name']); ?>">

                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($related['product_name']); ?></h6>
                        <p class="card-text text-muted small">
                            <?php echo getExcerpt($related['description'], 80); ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="product-price"><?php echo formatPrice($related['price']); ?></div>
                                <small class="product-unit">per <?php echo $related['unit']; ?></small>
                            </div>
                            <a href="product-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Inquiry Modal -->
<div class="modal fade" id="inquiryModal" tabindex="-1" aria-labelledby="inquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inquiryModalLabel">Inquiry untuk <?php echo htmlspecialchars($product['product_name']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                $flash = getFlashMessage();
                if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if (!isLoggedIn()): ?>
                <div class="text-center py-4">
                    <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                    <h5>Anda harus login untuk mengirim inquiry</h5>
                    <p class="text-muted">Silakan login terlebih dahulu untuk menghubungi kami tentang produk ini.</p>
                    <a href="login.php?redirect=product-detail.php?id=<?php echo $id; ?>" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                </div>
                <?php else: ?>
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">Nama Perusahaan</label>
                            <input type="text" class="form-control" id="company_name" name="company_name">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Telepon *</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Jumlah yang Diminta</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="quantity" name="quantity"
                                   value="<?php echo $product['min_order']; ?>" min="<?php echo $product['min_order']; ?>">
                            <span class="input-group-text"><?php echo $product['unit']; ?></span>
                        </div>
                        <small class="text-muted">Minimum order: <?php echo $product['min_order']; ?> <?php echo $product['unit']; ?></small>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan Tambahan</label>
                        <textarea class="form-control" id="message" name="message" rows="4"
                                  placeholder="Jelaskan kebutuhan spesifik Anda..."></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit_inquiry" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Inquiry
                        </button>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
