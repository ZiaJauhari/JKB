-- ============================================
-- Sample Data untuk Database global_export_indonesia
-- Gunakan file ini untuk mengisi data contoh
-- ============================================

USE global_export_indonesia;

-- ============================================
-- Sample Data: Products (dengan harga)
-- ============================================
INSERT INTO products (product_name, category, description, price, unit, min_order, image_url, stock_status, is_featured, is_active) VALUES
('Kopi Arabica Premium', 'pertanian', 'Kopi Arabica berkualitas tinggi dari dataran tinggi Indonesia dengan cita rasa khas dan aroma yang kuat. Cocok untuk pasar ekspor Eropa dan Amerika.', 25.50, 'kg', 100, 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e', 'available', TRUE, TRUE),
('Kopi Robusta Grade A', 'pertanian', 'Kopi Robusta pilihan dengan kadar kafein tinggi, ideal untuk espresso dan kopi instan. Populer di pasar Asia dan Timur Tengah.', 18.75, 'kg', 100, 'https://images.unsplash.com/photo-1447933601403-0c6688de566e', 'available', TRUE, TRUE),
('Kerajinan Rotan Set', 'kerajinan', 'Set kerajinan rotan handmade berkualitas ekspor meliputi keranjang, tempat penyimpanan, dan dekorasi rumah. Ramah lingkungan dan tahan lama.', 45.00, 'set', 50, 'https://images.unsplash.com/photo-1615529182904-14819c35db37', 'available', TRUE, TRUE),
('Batik Tulis Premium', 'kerajinan', 'Kain batik tulis asli Indonesia dengan motif tradisional dan modern. Setiap piece adalah karya seni unik yang dibuat oleh pengrajin berpengalaman.', 85.00, 'pcs', 20, 'https://images.unsplash.com/photo-1610701596007-11502861dcfa', 'available', TRUE, TRUE),
('Udang Beku Vannamei', 'perikanan', 'Udang vannamei beku berkualitas ekspor dengan size 31/40, 41/50, dan 51/60. Diproses dengan standar HACCP dan bersertifikat internasional.', 12.50, 'kg', 500, 'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47', 'available', TRUE, TRUE),
('Tuna Loin Frozen', 'perikanan', 'Tuna loin beku grade A untuk pasar Jepang dan Eropa. Fresh frozen dengan suhu terkontrol untuk menjaga kualitas dan kesegaran.', 28.00, 'kg', 200, 'https://images.unsplash.com/photo-1544943910-4c1dc44aab44', 'available', FALSE, TRUE),
('Minyak Kelapa Sawit (CPO)', 'pertanian', 'Crude Palm Oil (CPO) berkualitas ekspor dengan FFA rendah. Tersertifikasi RSPO untuk pasar yang peduli sustainability.', 850.00, 'ton', 20, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5', 'available', FALSE, TRUE),
('Kakao Beans Fermented', 'pertanian', 'Biji kakao fermentasi premium dari Sulawesi dengan flavor profile cokelat yang kompleks. Ideal untuk produsen cokelat premium.', 3.80, 'kg', 1000, 'https://images.unsplash.com/photo-1511381939415-e44015466834', 'available', TRUE, TRUE),
('Furniture Jati Minimalis', 'kerajinan', 'Furniture kayu jati solid dengan desain minimalis modern. Tahan lama dan cocok untuk pasar ekspor high-end.', 1200.00, 'set', 5, 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc', 'limited', FALSE, TRUE),
('Rempah-rempah Mix', 'pertanian', 'Paket rempah-rempah Indonesia meliputi cengkeh, pala, kayu manis, dan lada. Dikemas higienis untuk pasar retail internasional.', 15.00, 'kg', 100, 'https://images.unsplash.com/photo-1596040033229-a0b3b83b2e4d', 'available', FALSE, TRUE);

-- ============================================
-- Sample Data: Articles
-- ============================================
INSERT INTO articles (title, slug, excerpt, content, author, image_url, category, tags, is_published, published_at) VALUES
('Tips Memulai Ekspor untuk UMKM Indonesia', 'tips-memulai-ekspor-umkm', 'Panduan lengkap untuk UMKM yang ingin memulai ekspor produk ke pasar global. Pelajari langkah-langkah penting dan dokumen yang diperlukan.', 
'<h2>Mengapa UMKM Harus Ekspor?</h2><p>Ekspor membuka peluang pasar yang lebih luas dan meningkatkan pendapatan bisnis Anda. Dengan populasi dunia yang terus bertumbuh, permintaan produk berkualitas dari Indonesia semakin meningkat.</p><h2>Langkah-langkah Memulai Ekspor</h2><ol><li><strong>Riset Pasar</strong>: Identifikasi negara tujuan dan permintaan produk</li><li><strong>Legalitas</strong>: Urus SIUP, TDP, dan NIB</li><li><strong>Sertifikasi</strong>: Dapatkan sertifikat produk sesuai standar internasional</li><li><strong>Partner Ekspor</strong>: Cari partner ekspor terpercaya seperti Global Export Indonesia</li><li><strong>Dokumentasi</strong>: Siapkan invoice, packing list, dan dokumen ekspor lainnya</li></ol><h2>Dokumen yang Diperlukan</h2><p>Beberapa dokumen penting untuk ekspor meliputi: Commercial Invoice, Packing List, Bill of Lading, Certificate of Origin, dan dokumen khusus sesuai produk.</p>', 
'Admin NT', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40', 'Panduan Ekspor', 'ekspor,umkm,tips,panduan', TRUE, NOW()),

('Peluang Pasar Ekspor Indonesia 2024', 'peluang-pasar-ekspor-2024', 'Analisis peluang dan tren pasar ekspor global untuk produk Indonesia di tahun 2024. Negara mana yang paling potensial?', 
'<h2>Tren Ekspor 2024</h2><p>Tahun 2024 diprediksi menjadi tahun yang cerah untuk ekspor Indonesia. Beberapa sektor yang mengalami pertumbuhan signifikan adalah produk pertanian organik, kerajinan ramah lingkungan, dan produk perikanan berkelanjutan.</p><h2>Negara Tujuan Potensial</h2><ul><li><strong>Amerika Serikat</strong>: Permintaan tinggi untuk kopi, kakao, dan furniture</li><li><strong>Uni Eropa</strong>: Fokus pada produk organik dan sustainable</li><li><strong>China</strong>: Pasar besar untuk CPO dan produk perikanan</li><li><strong>Jepang</strong>: Premium market untuk seafood dan kerajinan</li><li><strong>Timur Tengah</strong>: Permintaan halal food dan rempah-rempah</li></ul><h2>Strategi Sukses</h2><p>Untuk sukses di pasar global, fokus pada kualitas produk, sertifikasi internasional, dan partnership dengan eksportir berpengalaman.</p>', 
'Admin GEI', 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e', 'Analisis Pasar', 'pasar,ekspor,2024,peluang', TRUE, NOW()),

('Pentingnya Sertifikasi Produk Ekspor', 'pentingnya-sertifikasi-produk-ekspor', 'Mengapa sertifikasi produk sangat penting untuk ekspor? Pelajari jenis-jenis sertifikasi yang dibutuhkan untuk berbagai pasar.', 
'<h2>Mengapa Sertifikasi Penting?</h2><p>Sertifikasi produk adalah kunci untuk memasuki pasar internasional. Setiap negara memiliki standar dan regulasi yang berbeda untuk produk impor.</p><h2>Jenis-jenis Sertifikasi</h2><ul><li><strong>HACCP</strong>: Untuk produk makanan dan perikanan</li><li><strong>Halal Certificate</strong>: Penting untuk pasar Muslim</li><li><strong>Organic Certificate</strong>: Untuk produk pertanian organik</li><li><strong>FSC</strong>: Untuk produk kayu dan furniture</li><li><strong>Fair Trade</strong>: Menunjukkan praktik perdagangan yang adil</li></ul><h2>Cara Mendapatkan Sertifikasi</h2><p>Hubungi lembaga sertifikasi yang terakreditasi dan siapkan dokumentasi produk Anda. Proses biasanya memakan waktu 2-6 bulan tergantung jenis sertifikasi.</p>', 
'Admin GEI', 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85', 'Sertifikasi', 'sertifikasi,ekspor,standar,kualitas', TRUE, NOW());

-- ============================================
-- Sample Data: Testimonials
-- ============================================
INSERT INTO testimonials (client_name, company_name, position, testimonial, rating, is_featured, is_active) VALUES
('Budi Santoso', 'CV Kopi Nusantara', 'Direktur', 'GEI membantu kami mengekspor kopi Arabica ke Eropa dengan lancar. Dokumentasi lengkap dan pengiriman tepat waktu. Sangat profesional dan responsif terhadap kebutuhan kami!', 5, TRUE, TRUE),
('Siti Aminah', 'Pengrajin Rotan Kalimantan', 'Owner', 'Profesional dan terpercaya! Produk kerajinan rotan kami berhasil masuk pasar Amerika berkat bantuan GEI. Mereka membantu dari sourcing hingga shipping. Highly recommended!', 5, TRUE, TRUE),
('Ahmad Hidayat', 'PT Bahari Sejahtera', 'Export Manager', 'Layanan ekspor perikanan yang sangat baik. Produk udang beku kami sampai dengan kondisi sempurna ke Jepang. Tim GEI sangat memahami handling produk perikanan.', 5, TRUE, TRUE),
('Dewi Lestari', 'UD Batik Nusantara', 'Pemilik', 'Berkat GEI, batik kami sekarang diekspor ke berbagai negara. Mereka membantu kami mendapatkan sertifikasi dan dokumentasi yang diperlukan. Terima kasih GEI!', 5, FALSE, TRUE),
('Rudi Hartono', 'PT Agro Makmur', 'CEO', 'Partner ekspor yang sangat reliable. Sudah 3 tahun kami bekerja sama untuk ekspor CPO dan hasilnya selalu memuaskan. Proses cepat dan harga kompetitif.', 5, FALSE, TRUE);

-- ============================================
-- Sample Data: Newsletter Users
-- ============================================
INSERT INTO newsletter_users (email, status) VALUES
('subscriber1@example.com', 'active'),
('subscriber2@example.com', 'active'),
('subscriber3@example.com', 'active');

-- ============================================
-- Sample Data: Inquiries
-- ============================================
INSERT INTO inquiries (full_name, company_name, email, phone, product_type, message, status) VALUES
('John Doe', 'ABC Trading Co', 'john@abctrading.com', '+1234567890', 'Kopi Arabica', 'Saya tertarik untuk mengimpor kopi arabica 5 ton ke Amerika. Mohon info harga dan MOQ.', 'new'),
('Jane Smith', 'European Imports Ltd', 'jane@euroimports.com', '+44123456789', 'Kerajinan Rotan', 'Looking for rattan furniture supplier for European market. Please send catalog and price list.', 'in_progress'),
('Ali Hassan', 'Middle East Foods', 'ali@mefoods.com', '+971123456789', 'Rempah-rempah', 'Need halal certified spices for UAE market. What certifications do you have?', 'new');

-- ============================================
-- Selesai! Sample data berhasil diinsert
-- ============================================
