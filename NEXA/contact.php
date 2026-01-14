<?php
$page_title = "Hubungi Kami";
require_once 'includes/header.php';

// Fetch active products for the dropdown
$db = getDB();
$products = $db->query("SELECT id, product_name FROM products WHERE is_active = TRUE ORDER BY product_name")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    
    // Get form data
    $full_name = sanitize($_POST['full_name']);
    $company_name = sanitize($_POST['company_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $product_type = sanitize($_POST['product_type']);
    $message = sanitize($_POST['message']);
    
    // Validate
    $errors = [];
    
    if (empty($full_name)) {
        $errors[] = "Nama lengkap harus diisi";
    }
    
    if (empty($email) || !validateEmail($email)) {
        $errors[] = "Email tidak valid";
    }
    
    if (empty($phone)) {
        $errors[] = "Nomor telepon harus diisi";
    }
    
    if (empty($message)) {
        $errors[] = "Pesan harus diisi";
    }
    
    if (empty($errors)) {
        try {
            // Insert inquiry
            $stmt = $db->prepare("INSERT INTO inquiries (full_name, company_name, email, phone, product_type, message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $company_name, $email, $phone, $product_type, $message]);
            
            setFlashMessage('success', 'Terima kasih! Inquiry Anda telah dikirim. Tim kami akan segera menghubungi Anda.');
            redirect('contact.php');
        } catch (Exception $e) {
            $errors[] = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
}
?>

<!-- Page Header -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Hubungi Kami</h1>
                <p class="lead text-muted mb-0">
                    Kami siap membantu Anda memulai ekspor produk ke pasar global
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-lg-end mb-0">
                        <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                        <li class="breadcrumb-item active">Kontak</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-4" data-aos="fade-right">
                <div class="contact-form">
                    <h3 class="mb-4">Kirim Inquiry</h3>
                    
                    <?php if (isset($errors) && count($errors) > 0): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required
                                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                                <div class="invalid-feedback">Nama lengkap harus diisi</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Nama Perusahaan</label>
                                <input type="text" class="form-control" id="company_name" name="company_name"
                                       value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                <div class="invalid-feedback">Email tidak valid</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" required
                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                                <div class="invalid-feedback">Nomor telepon harus diisi</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="product_type" class="form-label">Produk yang Diminati</label>
                            <select class="form-select" id="product_type" name="product_type">
                                <option value="">Pilih produk...</option>
                                <?php foreach($products as $product): ?>
                                <option value="<?php echo htmlspecialchars($product['product_name']); ?>" <?php echo (isset($_POST['product_type']) && $_POST['product_type'] == $product['product_name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($product['product_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Pesan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            <div class="invalid-feedback">Pesan harus diisi</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Inquiry
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-4 mb-4" data-aos="fade-left">
                <div class="contact-info-card">
                    <h4 class="mb-4">Informasi Kontak</h4>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6>Alamat</h6>
                            <p class="mb-0">Jl. Delima Dalam Blok E<br> Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75243<br>Indonesia</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h6>Telepon</h6>
                            <p class="mb-0">+62 21 1234 5678<br>+62 812 3456 7890</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6>Email</h6>
                            <p class="mb-0">info@nexatrade.com<br>support@nexatrade.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6>Jam Operasional</h6>
                            <p class="mb-0">Senin - Jumat: 08:00 - 17:00<br>Sabtu - Minggu: Tutup</p>
                        </div>
                    </div>
                    
                    <hr class="border-light my-4">
                    
                    <h6 class="mb-3">Ikuti Kami</h6>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-light btn-sm">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="btn btn-light btn-sm">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn btn-light btn-sm">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="btn btn-light btn-sm">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-0">
    <div class="container-fluid px-0">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d693.4039908800552!2d117.13991535370504!3d-0.4789514412194919!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2b3c630a13bc0b97%3A0xefec007c66cf31a7!2sKONSULTAN%20BORNEO!5e0!3m2!1sid!2sid!4v1768360058050!5m2!1sid!2sid" 
                width="100%" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
        </iframe>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Pertanyaan Umum</h2>
            <p class="text-muted">Jawaban untuk pertanyaan yang sering diajukan</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item" data-aos="fade-up">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Bagaimana cara memulai ekspor dengan Nexa Trade?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Anda dapat memulai dengan mengirimkan inquiry melalui form di atas atau menghubungi kami langsung. Tim kami akan membantu Anda dari konsultasi awal hingga pengiriman produk.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Apa saja dokumen yang diperlukan untuk ekspor?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Dokumen yang diperlukan meliputi: Commercial Invoice, Packing List, Bill of Lading, Certificate of Origin, dan sertifikasi produk sesuai negara tujuan. Tim kami akan membantu mempersiapkan semua dokumen.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Berapa lama proses ekspor?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Waktu proses bervariasi tergantung negara tujuan dan jenis produk, umumnya 2-4 minggu dari konfirmasi order hingga pengiriman. Kami menyediakan tracking untuk memantau status pengiriman.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item" data-aos="fade-up" data-aos-delay="300">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Apakah ada minimum order quantity (MOQ)?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ya, setiap produk memiliki MOQ yang berbeda. Informasi MOQ dapat dilihat di halaman detail produk atau hubungi kami untuk diskusi lebih lanjut.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
