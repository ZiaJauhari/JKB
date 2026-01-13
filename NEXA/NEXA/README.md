# Nexa Trade (Nusantara Export Asia) - Website Profesional

Website profesional untuk ekspor produk Indonesia ke pasar global dengan sistem manajemen konten lengkap.

## 🌟 Fitur Utama

### Frontend (Public Website)

- ✅ **Homepage** - Hero section, featured products, statistics, testimonials
- ✅ **Katalog Produk** - Filter kategori, search, pagination, detail produk
- ✅ **Tentang Kami** - Visi, misi, nilai-nilai, dan layanan
- ✅ **Artikel/Blog** - Panduan ekspor, tips, dan informasi
- ✅ **Kontak** - Form inquiry, informasi kontak, FAQ
- ✅ **Newsletter** - Subscription system dengan AJAX
- ✅ **Responsive Design** - Mobile-friendly dengan Bootstrap 5
- ✅ **Modern UI/UX** - Animasi AOS, smooth scrolling

### Backend (Admin Panel)

- ✅ **Dashboard** - Overview statistics dan recent data
- ✅ **Login System** - Secure authentication dengan password hashing
- ✅ **Product Management** - CRUD produk ekspor
- ✅ **Inquiry Management** - Kelola inquiry dari customer
- ✅ **Article Management** - CRUD artikel/blog
- ✅ **Testimonial Management** - Kelola testimoni klien
- ✅ **Newsletter Management** - Kelola users

## 📋 Struktur Database

Database: `global_export_indonesia`

### Tabel Utama:

1. **products** - Produk ekspor dengan harga
2. **inquiries** - Inquiry/konsultasi dari customer
3. **articles** - Artikel/blog tentang ekspor
4. **testimonials** - Testimoni dari klien
5. **newsletter_users** - Email users
6. **contact_messages** - Pesan dari form kontak
7. **admin_users** - User admin untuk login

## 🚀 Instalasi

### Persyaratan Sistem:

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau MariaDB 10.3+
- Apache/Nginx Web Server
- XAMPP/WAMP/LAMP (untuk development)

### Langkah Instalasi:

1. **Clone/Copy Project**

   ```bash
   # Copy semua file ke folder htdocs/wowo
   ```

2. **Import Database**

   ```bash
   # Buka phpMyAdmin atau MySQL client
   # Import file: create_database.sql
   # Import file: sample_data.sql (optional, untuk data contoh)
   ```

3. **Konfigurasi Database**

   Edit file `config/database.php` jika perlu:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'global_export_indonesia');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Set Permissions** (Linux/Mac)

   ```bash
   chmod -R 755 wowo/
   chmod -R 777 wowo/assets/images/uploads/
   ```

5. **Akses Website**
   - Frontend: `http://localhost/wowo/`
   - Admin Panel: `http://localhost/wowo/admin/login.php`

## 🔐 Login Admin

**Default Admin Account:**

- Username: `admin`
- Password: `admin123`

⚠️ **PENTING:** Segera ganti password default setelah login pertama!

## 📁 Struktur Folder

```
wowo/
├── admin/                  # Admin panel
│   ├── login.php          # Halaman login
│   ├── dashboard.php      # Dashboard admin
│   └── logout.php         # Logout
├── api/                   # API endpoints
│   └── newsletter.php     # Newsletter subscription API
├── assets/                # Asset files
│   ├── css/
│   │   └── style.css     # Custom styles
│   ├── js/
│   │   └── main.js       # Custom JavaScript
│   └── images/           # Images
├── config/               # Configuration
│   └── database.php      # Database connection
├── includes/             # Include files
│   ├── header.php        # Header template
│   ├── footer.php        # Footer template
│   └── functions.php     # Helper functions
├── index.php             # Homepage
├── products.php          # Products catalog
├── about.php             # About page
├── articles.php          # Articles listing
├── contact.php           # Contact page
├── create_database.sql   # Database schema
├── sample_data.sql       # Sample data
└── README.md            # Documentation
```

## 🎨 Teknologi yang Digunakan

### Frontend:

- **HTML5** - Semantic markup
- **CSS3** - Modern styling
- **Bootstrap 5** - Responsive framework
- **JavaScript (ES6+)** - Interactive features
- **Font Awesome** - Icons
- **AOS** - Scroll animations
- **Google Fonts** - Typography (Poppins)

### Backend:

- **PHP 8+** - Server-side scripting
- **PDO** - Database abstraction
- **MySQL/MariaDB** - Database management

### Security:

- **Password Hashing** - bcrypt algorithm
- **Prepared Statements** - SQL injection prevention
- **Input Sanitization** - XSS prevention
- **Session Management** - Secure authentication

## 📱 Fitur Responsive

Website fully responsive untuk semua device:

- 📱 Mobile (320px - 767px)
- 📱 Tablet (768px - 1023px)
- 💻 Desktop (1024px+)

## 🔧 Kustomisasi

### Mengubah Warna Theme:

Edit `assets/css/style.css`:

```css
:root {
  --primary-color: #0d6efd;
  --secondary-color: #198754;
}
```

### Menambah Produk:

1. Login ke admin panel
2. Navigasi ke menu "Produk"
3. Klik "Tambah Produk Baru"
4. Isi form dan upload gambar
5. Simpan

### Menambah Artikel:

1. Login ke admin panel
2. Navigasi ke menu "Artikel"
3. Klik "Tambah Artikel Baru"
4. Tulis konten dengan rich text editor
5. Publish

## 📊 Database Schema

### Products Table:

```sql
- id (INT, PRIMARY KEY)
- product_name (VARCHAR)
- category (ENUM: pertanian, kerajinan, perikanan, lainnya)
- description (TEXT)
- price (DECIMAL) - dalam USD
- unit (VARCHAR) - kg, ton, pcs, dll
- min_order (INT)
- image_url (VARCHAR)
- stock_status (ENUM)
- is_featured (BOOLEAN)
- is_active (BOOLEAN)
```

### Inquiries Table:

```sql
- id (INT, PRIMARY KEY)
- full_name (VARCHAR)
- company_name (VARCHAR)
- email (VARCHAR)
- phone (VARCHAR)
- product_type (VARCHAR)
- message (TEXT)
- status (ENUM: new, in_progress, completed, cancelled)
- created_at (TIMESTAMP)
```

## 🔒 Keamanan

### Best Practices yang Diterapkan:

1. ✅ Password hashing dengan `password_hash()`
2. ✅ Prepared statements untuk semua query
3. ✅ Input sanitization dan validation
4. ✅ Session management yang aman
5. ✅ HTTPS ready (gunakan SSL certificate)
6. ✅ File upload validation
7. ✅ XSS protection

### Rekomendasi Keamanan:

- Ganti password admin default
- Gunakan HTTPS di production
- Backup database secara berkala
- Update PHP dan dependencies
- Batasi akses folder admin

## 📈 Performance

### Optimasi yang Diterapkan:

- ✅ Lazy loading images
- ✅ Minified CSS/JS (production)
- ✅ Database indexing
- ✅ Efficient queries dengan pagination
- ✅ CDN untuk libraries (Bootstrap, Font Awesome)

## 🐛 Troubleshooting

### Database Connection Error:

```
Solution: Periksa kredensial di config/database.php
```

### Images Not Showing:

```
Solution: Periksa permissions folder assets/images/
chmod -R 777 assets/images/uploads/
```

### Admin Can't Login:

```
Solution: Pastikan database sudah di-import dengan benar
Cek tabel admin_users ada data default
```

### Newsletter Not Working:

```
Solution: Periksa path di api/newsletter.php
Pastikan database connection berfungsi
```

## 📞 Support

Untuk pertanyaan atau bantuan:

- Email: support@globalexportindonesia.com
- Website: https://globalexportindonesia.com

## 📝 License

Copyright © 2024 Nexa Trade (Nusantara Export Asia). All rights reserved.

## 🎯 Roadmap

### Future Features:

- [ ] Multi-language support (EN/ID)
- [ ] Payment gateway integration
- [ ] Live chat support
- [ ] Product comparison
- [ ] Advanced analytics
- [ ] Email notifications
- [ ] Export to PDF/Excel
- [ ] API documentation
- [ ] Mobile app

## 👨‍💻 Development

### Local Development:

```bash
# Start XAMPP/WAMP
# Access: http://localhost/wowo/

# For live reload (optional):
npm install -g browser-sync
browser-sync start --proxy "localhost/wowo" --files "**/*.php, **/*.css, **/*.js"
```

### Production Deployment:

1. Upload files via FTP/SFTP
2. Import database
3. Update config/database.php
4. Set proper permissions
5. Enable HTTPS
6. Test all features

## 🙏 Credits

- Bootstrap 5 - https://getbootstrap.com
- Font Awesome - https://fontawesome.com
- AOS - https://michalsnik.github.io/aos/
- Unsplash - https://unsplash.com (images)

---

**Built with ❤️ for Indonesian Exporters**

Last Updated: 2024
Version: 1.0.0
