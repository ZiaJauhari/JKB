<?php
$page_title = "Artikel & Blog";
require_once 'includes/header.php';

// Get database connection
$db = getDB();

// Get filter parameters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 9;

// Build query
$where = ["is_published = 1"];
$params = [];

if ($category) {
    $where[] = "category = :category";
    $params[':category'] = $category;
}

if ($search) {
    $where[] = "(title LIKE :search OR excerpt LIKE :search OR content LIKE :search)";
    $params[':search'] = "%$search%";
}

$where_clause = implode(' AND ', $where);

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM articles WHERE $where_clause";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetch()['total'];

// Calculate pagination
$pagination = paginate($total_items, $items_per_page, $page);

// Get articles
$sql = "SELECT * FROM articles WHERE $where_clause ORDER BY published_at DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

// Get categories
$categories_sql = "SELECT DISTINCT category FROM articles WHERE is_published = 1 AND category IS NOT NULL ORDER BY category";
$categories = $db->query($categories_sql)->fetchAll(PDO::FETCH_COLUMN);

// Get popular articles
$popular_sql = "SELECT * FROM articles WHERE is_published = 1 ORDER BY views DESC LIMIT 5";
$popular_articles = $db->query($popular_sql)->fetchAll();
?>

<!-- Page Header -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Artikel & Blog</h1>
                <p class="lead text-muted mb-0">
                    Tips, panduan, dan informasi seputar ekspor produk Indonesia
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-lg-end mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                        <li class="breadcrumb-item active">Artikel</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Articles Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8 mb-4">
                <!-- Search & Filter -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Cari artikel..." 
                                       value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Results Info -->
                <div class="mb-4">
                    <h5>Menampilkan <?php echo count($articles); ?> dari <?php echo $total_items; ?> artikel</h5>
                    <?php if($search): ?>
                    <small class="text-muted">Hasil pencarian untuk: "<?php echo htmlspecialchars($search); ?>"</small>
                    <?php endif; ?>
                </div>
                
                <?php if(count($articles) > 0): ?>
                <!-- Articles List -->
                <?php foreach($articles as $article): ?>
                <article class="card article-card mb-4 shadow-sm" data-aos="fade-up">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="<?php echo $article['image_url']; ?>" 
                                 class="img-fluid h-100 w-100" 
                                 style="object-fit: cover;"
                                 alt="<?php echo htmlspecialchars($article['title']); ?>">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <?php if($article['category']): ?>
                                <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($article['category']); ?></span>
                                <?php endif; ?>
                                
                                <h4 class="card-title">
                                    <a href="article-detail.php?slug=<?php echo $article['slug']; ?>" class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </a>
                                </h4>
                                
                                <div class="article-meta mb-3">
                                    <i class="far fa-calendar me-1"></i>
                                    <?php echo formatDate($article['published_at']); ?>
                                    <span class="mx-2">•</span>
                                    <i class="far fa-user me-1"></i>
                                    <?php echo htmlspecialchars($article['author']); ?>
                                    <span class="mx-2">•</span>
                                    <i class="far fa-eye me-1"></i>
                                    <?php echo $article['views']; ?> views
                                </div>
                                
                                <p class="card-text text-muted">
                                    <?php echo getExcerpt(strip_tags($article['excerpt']), 150); ?>
                                </p>
                                
                                <a href="article-detail.php?slug=<?php echo $article['slug']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-right me-1"></i>Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
                
                <!-- Pagination -->
                <?php if($pagination['total_pages'] > 1): ?>
                <nav aria-label="Article pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . $search : ''; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if($i == 1 || $i == $pagination['total_pages'] || abs($i - $page) <= 2): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . $search : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php elseif(abs($i - $page) == 3): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo $page >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . $search : ''; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                <!-- No Results -->
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-5x text-muted mb-4"></i>
                    <h3>Artikel Tidak Ditemukan</h3>
                    <p class="text-muted mb-4">Maaf, tidak ada artikel yang sesuai dengan pencarian Anda.</p>
                    <a href="articles.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Semua Artikel
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Categories -->
                <?php if(count($categories) > 0): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-folder me-2"></i>Kategori
                        </h5>
                        <div class="list-group list-group-flush">
                            <a href="articles.php" class="list-group-item list-group-item-action <?php echo !$category ? 'active' : ''; ?>">
                                Semua Artikel
                            </a>
                            <?php foreach($categories as $cat): ?>
                            <a href="articles.php?category=<?php echo urlencode($cat); ?>" 
                               class="list-group-item list-group-item-action <?php echo $category == $cat ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Popular Articles -->
                <?php if(count($popular_articles) > 0): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-fire me-2"></i>Artikel Populer
                        </h5>
                        <?php foreach($popular_articles as $popular): ?>
                        <div class="d-flex mb-3 pb-3 border-bottom">
                            <img src="<?php echo $popular['image_url']; ?>" 
                                 alt="<?php echo htmlspecialchars($popular['title']); ?>"
                                 class="rounded me-3"
                                 style="width: 80px; height: 80px; object-fit: cover;">
                            <div>
                                <h6 class="mb-1">
                                    <a href="article-detail.php?slug=<?php echo $popular['slug']; ?>" 
                                       class="text-decoration-none text-dark">
                                        <?php echo getExcerpt($popular['title'], 60); ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="far fa-eye me-1"></i><?php echo $popular['views']; ?> views
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Newsletter -->
                <div class="card shadow-sm bg-gradient-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-envelope-open-text fa-3x mb-3"></i>
                        <h5 class="card-title">Newsletter</h5>
                        <p class="card-text">Dapatkan artikel terbaru langsung ke email Anda</p>
                        <form id="sidebarNewsletterForm">
                            <div class="input-group">
                                <input type="email" class="form-control" name="email" placeholder="Email Anda" required>
                                <button class="btn btn-light" type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
$extra_js = '
<script>
document.getElementById("sidebarNewsletterForm")?.addEventListener("submit", async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const response = await fetch("api/newsletter.php", {
            method: "POST",
            body: formData
        });
        
        const result = await response.json();
        
        if(result.success) {
            alert("Terima kasih! Anda telah berlangganan newsletter kami.");
            this.reset();
        } else {
            alert(result.message || "Terjadi kesalahan. Silakan coba lagi.");
        }
    } catch(error) {
        alert("Terjadi kesalahan. Silakan coba lagi.");
    }
});
</script>
';
require_once 'includes/footer.php'; 
?>
