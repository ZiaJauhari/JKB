<?php
$page_title = "404 - Halaman Tidak Ditemukan";
require_once 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="py-5">
                    <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                    <h1 class="display-1 fw-bold text-primary">404</h1>
                    <h2 class="mb-4">Halaman Tidak Ditemukan</h2>
                    <p class="lead text-muted mb-4">
                        Maaf, halaman yang Anda cari tidak dapat ditemukan. 
                        Halaman mungkin telah dipindahkan atau dihapus.
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="index.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Kembali ke Beranda
                        </a>
                        <a href="contact.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-envelope me-2"></i>Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
