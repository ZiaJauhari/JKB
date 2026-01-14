<?php
$page_title = "Beranda";
require_once 'includes/header.php';

// Get database connection
$db = getDB();

// Get featured products
$stmt = $db->prepare("SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 LIMIT 6");
$stmt->execute();
$featured_products = $stmt->fetchAll();

// Get testimonials
$stmt = $db->prepare("SELECT * FROM testimonials WHERE is_featured = 1 AND is_active = 1 LIMIT 3");
$stmt->execute();
$testimonials = $stmt->fetchAll();

// Get latest articles
$stmt = $db->prepare("SELECT * FROM articles WHERE is_published = 1 ORDER BY published_at DESC LIMIT 3");
$stmt->execute();
$articles = $stmt->fetchAll();

// Get statistics
$stmt = $db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
$total_products = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM inquiries");
$total_inquiries = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM testimonials WHERE is_active = 1");
$total_testimonials = $stmt->fetch()['total'];
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="display-4 fw-bold mb-4">
                    Ekspor Produk Indonesia ke Pasar Global
                </h1>
                <p class="lead mb-4">
                    Partner terpercaya untuk mengekspor produk berkualitas Indonesia. 
                    Kami membantu UMKM dan perusahaan menembus pasar internasional dengan mudah dan aman.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="products.php" class="btn btn-light btn-lg">
                        <i class="fas fa-box me-2"></i>Lihat Produk
                    </a>
                    <a href="contact.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-envelope me-2"></i>Hubungi Kami
                    </a>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0" data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800" 
                     alt="Export" class="img-fluid rounded-custom shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6 mb-4" data-aos="fade-up" data-aos-delay="0">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="stat-number" data-target="<?php echo $total_products; ?>">0</div>
                    <div class="stat-label">Produk Ekspor</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="stat-number" data-target="25">0</div>
                    <div class="stat-label">Negara Tujuan</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="stat-number" data-target="<?php echo $total_inquiries; ?>">0</div>
                    <div class="stat-label">Inquiry Diterima</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-number" data-target="<?php echo $total_testimonials; ?>">0</div>
                    <div class="stat-label">Klien Puas</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Produk Unggulan</h2>
            <p class="text-muted">Produk ekspor berkualitas tinggi dari Indonesia</p>
        </div>
        
        <div class="row">
            <?php foreach($featured_products as $product): ?>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo array_search($product, $featured_products) * 100; ?>">
                <div class="card product-card h-100">
                    <?php if($product['stock_status'] == 'limited'): ?>
                    <span class="badge bg-warning product-badge">Stok Terbatas</span>
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
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                                <small class="product-unit">per <?php echo $product['unit']; ?></small>
                            </div>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-info-circle me-1"></i>Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="products.php" class="btn btn-primary btn-lg">
                <i class="fas fa-th me-2"></i>Lihat Semua Produk
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Mengapa Memilih Kami?</h2>
            <p class="text-muted">Keunggulan layanan ekspor kami</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="0">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h5>Produk Bersertifikat</h5>
                    <p class="text-muted">Semua produk memiliki sertifikasi internasional yang diperlukan</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h5>Pengiriman Cepat</h5>
                    <p class="text-muted">Logistik terpercaya dengan tracking real-time</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5>Support 24/7</h5>
                    <p class="text-muted">Tim customer service siap membantu kapan saja</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-box">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Transaksi Aman</h5>
                    <p class="text-muted">Sistem pembayaran yang aman dan terpercaya</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<?php if(count($testimonials) > 0): ?>
<section class="py-5">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Testimoni Klien</h2>
            <p class="text-muted">Apa kata mereka tentang layanan kami</p>
        </div>
        
        <div class="row">
            <?php foreach($testimonials as $testimonial): ?>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo array_search($testimonial, $testimonials) * 100; ?>">
                <div class="testimonial-card">
                    <div class="testimonial-rating mb-3">
                        <?php for($i = 0; $i < $testimonial['rating']; $i++): ?>
                        <i class="fas fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="mb-4">"<?php echo htmlspecialchars($testimonial['testimonial']); ?>"</p>
                    <div class="d-flex align-items-center">
                        <img src="<?php echo $testimonial['image_url'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($testimonial['client_name']); ?>" 
                             alt="<?php echo htmlspecialchars($testimonial['client_name']); ?>" 
                             class="testimonial-avatar me-3">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($testimonial['client_name']); ?></h6>
                            <small class="text-muted">
                                <?php echo htmlspecialchars($testimonial['position']); ?> - 
                                <?php echo htmlspecialchars($testimonial['company_name']); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Articles Section -->
<?php if(count($articles) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Artikel Terbaru</h2>
            <p class="text-muted">Tips dan panduan ekspor untuk Anda</p>
        </div>
        
        <div class="row">
            <?php foreach($articles as $article): ?>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo array_search($article, $articles) * 100; ?>">
                <div class="card article-card h-100">
                    <img src="<?php echo $article['image_url']; ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($article['title']); ?>">
                    <div class="card-body">
                        <div class="article-meta mb-2">
                            <i class="far fa-calendar me-1"></i>
                            <?php echo formatDate($article['published_at']); ?>
                            <span class="mx-2">•</span>
                            <i class="far fa-eye me-1"></i>
                            <?php echo $article['views']; ?> views
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($article['title']); ?></h5>
                        <p class="card-text text-muted">
                            <?php echo getExcerpt(strip_tags($article['excerpt']), 120); ?>
                        </p>
                        <a href="article-detail.php?slug=<?php echo $article['slug']; ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-right me-1"></i>Baca Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="articles.php" class="btn btn-primary btn-lg">
                <i class="fas fa-newspaper me-2"></i>Lihat Semua Artikel
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-5 bg-gradient-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h2 class="mb-3">Siap Mengekspor Produk Anda?</h2>
                <p class="lead mb-0">
                    Hubungi kami sekarang dan mulai perjalanan ekspor Anda bersama Nexa Trade
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0" data-aos="fade-left">
                <a href="contact.php" class="btn btn-light btn-lg">
                    <i class="fas fa-paper-plane me-2"></i>Konsultasi Gratis
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
