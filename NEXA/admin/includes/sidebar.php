<?php
// Set defaults for session variables if not set
$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Administrator';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'admin';
?>
<div class="col-md-3 col-lg-2 px-0 sidebar">
    <div class="p-3">
        <h4 class="text-center mb-4">
            <i class="fas fa-globe-asia me-2"></i>NEXA Admin
        </h4>

        <div class="text-center mb-4">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=fff&color=0d6efd"
                 alt="Admin" class="rounded-circle mb-2" width="80">
            <h6 class="mb-0"><?php echo htmlspecialchars($admin_name); ?></h6>
            <small class="text-white-50"><?php echo ucfirst($admin_role); ?></small>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-home me-2"></i>Dashboard
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' || basename($_SERVER['PHP_SELF']) == 'product-add.php' || basename($_SERVER['PHP_SELF']) == 'product-edit.php' ? 'active' : ''; ?>" href="products.php">
                <i class="fas fa-box me-2"></i>Produk
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'inquiries.php' ? 'active' : ''; ?>" href="inquiries.php">
                <i class="fas fa-envelope me-2"></i>Inquiry
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'articles.php' || basename($_SERVER['PHP_SELF']) == 'article-add.php' || basename($_SERVER['PHP_SELF']) == 'article-edit.php' ? 'active' : ''; ?>" href="articles.php">
                <i class="fas fa-newspaper me-2"></i>Artikel
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'testimonials.php' || basename($_SERVER['PHP_SELF']) == 'testimonial-add.php' || basename($_SERVER['PHP_SELF']) == 'testimonial-edit.php' ? 'active' : ''; ?>" href="testimonials.php">
                <i class="fas fa-star me-2"></i>Testimoni
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">
                <i class="fas fa-users me-2"></i>Users
            </a>
            <hr class="border-light">
            <a class="nav-link" href="../index.php" target="_blank">
                <i class="fas fa-external-link-alt me-2"></i>Lihat Website
            </a>
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </nav>
    </div>
</div>
