<?php
$page_title = "Detail Produk";
require_once 'includes/header.php';

// Get database connection
$db = getDB();

// Get product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = (int)$_GET['id'];

// Get product details
$stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit();
}

// Check if user is logged in before allowing inquiry submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isUserLoggedIn()) {
    // Redirect to login page with return URL
    $current_url = urlencode($_SERVER['REQUEST_URI']);
    redirect('login.php?redirect=' . $current_url);
    exit();
}

// Handle inquiry form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isUserLoggedIn()) {
    $full_name = sanitize($_POST['full_name']);
    $company_name = sanitize($_POST['company_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $quantity = (int)$_POST['quantity'];
    $inquiry_message = sanitize($_POST['message']);

    // Validation
    $errors = [];
    if (empty($full_name)) $errors[] = "Nama lengkap harus diisi";
    if (empty($email) || !validateEmail($email)) $errors[] = "Email valid harus diisi";
    if (empty($phone)) $errors[] = "Nomor telepon harus diisi";
    if ($quantity < $product['min_order']) $errors[] = "Jumlah minimum order adalah " . $product['min_order'] . " " . $product['unit'];

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO inquiries (full_name, company_name, email, phone, product_type, product_id, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'new')");
            $stmt->execute([
                $full_name,
                $company_name,
                $email,
                $phone,
                $product['product_name'],
                $product['id'],
                "Inquiry untuk produk: " . $product['product_name'] . "\nJumlah: " . $quantity . " " . $product['unit'] . "\n\n" . $inquiry_message
            ]);

            $message = "Inquiry berhasil dikirim! Tim kami akan menghubungi Anda dalam 1-2 hari kerja.";
            $message_type = "success";

            // Send email notification (optional)
            $email_subject = "New Product Inquiry: " . $product['product_name'];
            $email_body = "New inquiry received:\n\n" .
                         "Product: " . $product['product_name'] . "\n" .
                         "Name: " . $full_name . "\n" .
                         "Company: " . $company_name . "\n" .
                         "Email: " . $email . "\n" .
                         "Phone: " . $phone . "\n" .
                         "Quantity: " . $quantity . " " . $product['unit'] . "\n\n" .
                         "Message: " . $inquiry_message;

            // Uncomment to send email
            // sendEmail('admin@nexatrade.com', $email_subject, $email_body);

        } catch (Exception $e) {
            $message = "Terjadi kesalahan. Silakan coba lagi.";
            $message_type = "danger";
        }
    } else {
        $message = implode("<br>", $errors);
        $message_type = "danger";
    }
}

// Get related products (same category, excluding current product)
$stmt = $db->prepare("SELECT * FROM products WHERE category = ? AND id != ? AND is_active = 1 ORDER BY RAND() LIMIT 4");
$stmt->execute([$product['category'], $product['id']]);
$related_products = $stmt->fetchAll();
?>

<!-- Page Header -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="products.php">Produk</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['product_name']); ?></li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge bg-<?php echo getCategoryBadge($product['category']); ?> fs-6">
                        <i class="fas fa-tag me-1"></i><?php echo ucfirst($product['category']); ?>
                    </span>
                    <?php if($product['is_featured']): ?>
                    <span class="badge bg-primary fs-6">
                        <i class="fas fa-star me-1"></i>Featured
                    </span>
                    <?php endif; ?>
                    <span class="badge bg-<?php
                        if($product['stock_status'] == 'available') echo 'success';
                        elseif($product['stock_status'] == 'limited') echo 'warning';
                        else echo 'danger';
                    ?> fs-6">
                        <i class="fas fa-<?php
                            if($product['stock_status'] == 'available') echo 'check-circle';
                            elseif($product['stock_status'] == 'limited') echo 'exclamation-triangle';
                            else echo 'times-circle';
                        ?> me-1"></i>
                        <?php
                        if($product['stock_status'] == 'available') echo 'Tersedia';
                        elseif($product['stock_status'] == 'limited') echo 'Stok Terbatas';
                        else echo 'Habis';
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Detail Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Product Image & Info -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <div class="product-image-container">
                                <img src="<?php echo $product['image_url']; ?>"
                                     class="img-fluid rounded-start product-detail-image"
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-body h-100 d-flex flex-column">
                                <div class="mb-auto">
                                    <h3 class="card-title mb-3"><?php echo htmlspecialchars($product['product_name']); ?></h3>

                                    <div class="product-price-section mb-4">
                                        <div class="d-flex align-items-baseline gap-2 mb-2">
                                            <span class="product-price-large"><?php echo formatPrice($product['price']); ?></span>
                                            <small class="text-muted">per <?php echo $product['unit']; ?></small>
                                        </div>
                                        <div class="text-muted small">
                                            Minimum Order: <?php echo $product['min_order']; ?> <?php echo $product['unit']; ?>
                                        </div>
                                    </div>

                                    <div class="product-description mb-4">
                                        <h5 class="mb-3">Deskripsi Produk</h5>
                                        <p class="card-text"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <i class="fas fa-box fa-2x text-primary mb-2"></i>
                                                <div class="small text-muted">Kategori</div>
                                                <div class="fw-bold"><?php echo ucfirst($product['category']); ?></div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <i class="fas fa-weight fa-2x text-success mb-2"></i>
                                                <div class="small text-muted">Satuan</div>
                                                <div class="fw-bold"><?php echo $product['unit']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inquiry Form -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-envelope me-2"></i>Request Quote
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <p class="text-muted small mb-4">
                            Isi formulir di bawah ini untuk mendapatkan penawaran harga dan informasi lebih lanjut tentang produk ini.
                        </p>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required
                                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="company_name" class="form-label">Nama Perusahaan</label>
                                <input type="text" class="form-control" id="company_name" name="company_name"
                                       value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required
                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Jumlah Pesanan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="quantity" name="quantity" required
                                           min="<?php echo $product['min_order']; ?>"
                                           value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : $product['min_order']; ?>">
                                    <span class="input-group-text"><?php echo $product['unit']; ?></span>
                                </div>
                                <small class="text-muted">Minimum: <?php echo $product['min_order']; ?> <?php echo $product['unit']; ?></small>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Pesan Tambahan</label>
                                <textarea class="form-control" id="message" name="message" rows="3"
                                          placeholder="Jelaskan kebutuhan spesifik Anda..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Inquiry
                            </button>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Response time: 1-2 hari kerja
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products Section -->
<?php if (count($related_products) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">Produk Terkait</h2>
            </div>
        </div>
        <div class="row">
            <?php foreach($related_products as $related): ?>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up">
                <div class="card product-card h-100">
                    <?php if($related['is_featured']): ?>
                    <span class="badge bg-primary product-badge">Featured</span>
                    <?php endif; ?>

                    <img src="<?php echo $related['image_url']; ?>"
                         class="card-img-top"
                         alt="<?php echo htmlspecialchars($related['product_name']); ?>">

                    <div class="card-body">
                        <span class="badge bg-<?php echo getCategoryBadge($related['category']); ?> mb-2">
                            <?php echo ucfirst($related['category']); ?>
                        </span>
                        <h6 class="card-title"><?php echo htmlspecialchars($related['product_name']); ?></h6>
                        <p class="card-text text-muted small">
                            <?php echo getExcerpt($related['description'], 80); ?>
                        </p>

                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="product-price"><?php echo formatPrice($related['price']); ?></div>
                                    <small class="product-unit">per <?php echo $related['unit']; ?></small>
                                </div>
                                <a href="product-detail.php?id=<?php echo $related['id']; ?>"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-info-circle me-1"></i>Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
.product-image-container {
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.product-detail-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-price-large {
    font-size: 2rem;
    font-weight: bold;
    color: #0d6efd;
}

.product-price-section {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    background: #f8f9fa;
}

.product-description {
    line-height: 1.6;
}

.sticky-top {
    z-index: 100;
}

@media (max-width: 768px) {
    .product-image-container {
        height: 300px;
    }

    .product-price-large {
        font-size: 1.5rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>
