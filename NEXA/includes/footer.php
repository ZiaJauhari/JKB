<!-- Footer -->
    <footer class="bg-dark text-light pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <!-- About Section -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-globe-asia me-2"></i>Nexa Trade
                    </h5>
                    <p class="text-light-50 small mb-1">
                        (Nusantara Export Asia)
                    </p>
                    <p class="text-light-50">
                        Partner terpercaya untuk ekspor produk berkualitas Indonesia ke pasar global. 
                        Kami membantu UMKM dan perusahaan Indonesia menembus pasar internasional.
                    </p>
                    <div class="social-links mt-3">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-linkedin fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="text-primary mb-3">Menu</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-light text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Beranda</a></li>
                        <li class="mb-2"><a href="products.php" class="text-light text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Produk</a></li>
                        <li class="mb-2"><a href="about.php" class="text-light text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Tentang Kami</a></li>
                        <li class="mb-2"><a href="articles.php" class="text-light text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Artikel</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-light text-decoration-none"><i class="fas fa-chevron-right me-2"></i>Kontak</a></li>
                    </ul>
                </div>
                
                <!-- Products Category -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-primary mb-3">Kategori Produk</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="products.php?category=pertanian" class="text-light text-decoration-none"><i class="fas fa-leaf me-2"></i>Pertanian</a></li>
                        <li class="mb-2"><a href="products.php?category=kerajinan" class="text-light text-decoration-none"><i class="fas fa-palette me-2"></i>Kerajinan</a></li>
                        <li class="mb-2"><a href="products.php?category=perikanan" class="text-light text-decoration-none"><i class="fas fa-fish me-2"></i>Perikanan</a></li>
                        <li class="mb-2"><a href="products.php?category=lainnya" class="text-light text-decoration-none"><i class="fas fa-box me-2"></i>Lainnya</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info & Newsletter -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-primary mb-3">Kontak Kami</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            Samarinda, Indonesia
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2 text-primary"></i>
                            +62 21 1234 5678
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            info@nexatrade.com
                        </li>
                    </ul>
                    
                    <h6 class="text-primary mt-4 mb-3">Newsletter</h6>
                    <form id="newsletterForm" class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control" name="email" placeholder="Email Anda" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <small class="text-light-50 d-block mt-2">Dapatkan update produk & tips ekspor</small>
                    </form>
                </div>
            </div>
            
            <hr class="border-secondary my-4">
            
            <!-- Copyright -->
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-light-50">
                        &copy; <?php echo date('Y'); ?> Nexa Trade (Nusantara Export Asia). All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-light-50">
                        Made with <i class="fas fa-heart text-danger"></i> in Indonesia
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <button id="backToTop" class="btn btn-primary btn-floating" title="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    
    <?php if(isset($extra_js)) echo $extra_js; ?>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
        
        // Newsletter form submission
        document.getElementById('newsletterForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('api/newsletter.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if(result.success) {
                    alert('Terima kasih! Anda telah berlangganan newsletter kami.');
                    this.reset();
                } else {
                    alert(result.message || 'Terjadi kesalahan. Silakan coba lagi.');
                }
            } catch(error) {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    </script>
</body>
</html>
