# 📦 Panduan Instalasi - Nexa Trade (Nusantara Export Asia)

Panduan lengkap untuk menginstal website Nexa Trade (Nusantara Export Asia) di server lokal atau production.

## 📋 Daftar Isi

1. [Persyaratan Sistem](#persyaratan-sistem)
2. [Instalasi di XAMPP (Windows)](#instalasi-di-xampp-windows)
3. [Instalasi di LAMP (Linux)](#instalasi-di-lamp-linux)
4. [Instalasi di MAMP (Mac)](#instalasi-di-mamp-mac)
5. [Konfigurasi Database](#konfigurasi-database)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

---

## 🖥️ Persyaratan Sistem

### Minimum Requirements:

- **PHP**: 7.4 atau lebih tinggi
- **MySQL**: 5.7+ atau MariaDB 10.3+
- **Apache**: 2.4+ dengan mod_rewrite enabled
- **RAM**: 512 MB minimum
- **Storage**: 100 MB free space

### Recommended:

- **PHP**: 8.0+
- **MySQL**: 8.0+ atau MariaDB 10.6+
- **RAM**: 1 GB+
- **Storage**: 500 MB+

### PHP Extensions Required:

- PDO
- PDO_MySQL
- mbstring
- openssl
- json
- fileinfo
- gd (untuk image processing)

---

## 🪟 Instalasi di XAMPP (Windows)

### Langkah 1: Download dan Install XAMPP

1. Download XAMPP dari: https://www.apachefriends.org/
2. Install XAMPP di `C:\xampp\`
3. Jalankan XAMPP Control Panel
4. Start Apache dan MySQL

### Langkah 2: Copy Files

1. Copy folder `wowo` ke `C:\xampp\htdocs\`
2. Struktur folder: `C:\xampp\htdocs\wowo\`

### Langkah 3: Import Database

1. Buka browser, akses: `http://localhost/phpmyadmin`
2. Klik tab "SQL"
3. Copy-paste isi file `create_database.sql`
4. Klik "Go" untuk execute
5. (Optional) Import `sample_data.sql` untuk data contoh

### Langkah 4: Konfigurasi

1. Buka file `config/database.php`
2. Pastikan konfigurasi sesuai:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'global_export_indonesia');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

### Langkah 5: Test Website

1. Buka browser
2. Akses: `http://localhost/wowo/`
3. Login admin: `http://localhost/wowo/admin/login.php`
   - Username: `admin`
   - Password: `admin123`

---

## 🐧 Instalasi di LAMP (Linux)

### Langkah 1: Install LAMP Stack

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y

# Install MySQL
sudo apt install mysql-server -y

# Install PHP
sudo apt install php libapache2-mod-php php-mysql php-mbstring php-xml php-gd -y

# Enable mod_rewrite
sudo a2enmod rewrite

# Restart Apache
sudo systemctl restart apache2
```

### Langkah 2: Copy Files

```bash
# Copy project ke web root
sudo cp -r wowo /var/www/html/

# Set permissions
sudo chown -R www-data:www-data /var/www/html/wowo
sudo chmod -R 755 /var/www/html/wowo
sudo chmod -R 777 /var/www/html/wowo/assets/images/uploads
```

### Langkah 3: Setup Database

```bash
# Login ke MySQL
sudo mysql -u root -p

# Di MySQL prompt:
CREATE DATABASE global_export_indonesia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'gei_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON global_export_indonesia.* TO 'gei_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import database
mysql -u root -p global_export_indonesia < /var/www/html/wowo/create_database.sql
mysql -u root -p global_export_indonesia < /var/www/html/wowo/sample_data.sql
```

### Langkah 4: Update Config

```bash
# Edit database config
sudo nano /var/www/html/wowo/config/database.php

# Update credentials:
define('DB_HOST', 'localhost');
define('DB_NAME', 'global_export_indonesia');
define('DB_USER', 'gei_user');
define('DB_PASS', 'your_password');
```

### Langkah 5: Configure Apache

```bash
# Create virtual host (optional)
sudo nano /etc/apache2/sites-available/gei.conf

# Add:
<VirtualHost *:80>
    ServerName gei.local
    DocumentRoot /var/www/html/wowo

    <Directory /var/www/html/wowo>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/gei_error.log
    CustomLog ${APACHE_LOG_DIR}/gei_access.log combined
</VirtualHost>

# Enable site
sudo a2ensite gei.conf
sudo systemctl reload apache2

# Add to hosts file
sudo nano /etc/hosts
# Add: 127.0.0.1 gei.local
```

### Langkah 6: Test

```bash
# Access website
http://localhost/wowo/
# or
http://gei.local/
```

---

## 🍎 Instalasi di MAMP (Mac)

### Langkah 1: Install MAMP

1. Download MAMP dari: https://www.mamp.info/
2. Install MAMP
3. Start MAMP servers

### Langkah 2: Copy Files

```bash
# Copy to MAMP htdocs
cp -r wowo /Applications/MAMP/htdocs/
```

### Langkah 3: Import Database

1. Akses: `http://localhost:8888/phpMyAdmin/`
2. Import `create_database.sql`
3. Import `sample_data.sql` (optional)

### Langkah 4: Update Config

```bash
# Edit config
nano /Applications/MAMP/htdocs/wowo/config/database.php

# Update if needed (default MAMP settings usually work)
```

### Langkah 5: Test

```
http://localhost:8888/wowo/
```

---

## 🗄️ Konfigurasi Database

### Manual Database Creation

```sql
-- Create database
CREATE DATABASE IF NOT EXISTS global_export_indonesia
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Use database
USE global_export_indonesia;

-- Import tables (copy from create_database.sql)
-- Import sample data (copy from sample_data.sql)
```

### Verify Database

```sql
-- Check tables
SHOW TABLES;

-- Check admin user
SELECT * FROM admin_users;

-- Check sample products
SELECT * FROM products LIMIT 5;
```

---

## ✅ Testing

### 1. Test Frontend

- [ ] Homepage loads correctly
- [ ] Products page shows items
- [ ] Search and filter work
- [ ] Contact form submits
- [ ] Newsletter subscription works
- [ ] All images load
- [ ] Responsive on mobile

### 2. Test Admin Panel

- [ ] Login works with admin/admin123
- [ ] Dashboard shows statistics
- [ ] Can view inquiries
- [ ] Can view products
- [ ] Can view articles
- [ ] Can view users
- [ ] Logout works

### 3. Test Database

```sql
-- Test queries
SELECT COUNT(*) FROM products;
SELECT COUNT(*) FROM inquiries;
SELECT COUNT(*) FROM articles;
```

---

## 🔧 Troubleshooting

### Problem: Database Connection Error

**Solution:**

```php
// Check config/database.php
// Verify credentials
// Test MySQL connection:
mysql -u root -p
```

### Problem: 404 Error on All Pages

**Solution:**

```bash
# Enable mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# Check .htaccess exists
ls -la /var/www/html/wowo/.htaccess
```

### Problem: Permission Denied

**Solution:**

```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/html/wowo
sudo chmod -R 755 /var/www/html/wowo
sudo chmod -R 777 /var/www/html/wowo/assets/images/uploads
```

### Problem: Images Not Uploading

**Solution:**

```bash
# Create upload directory
mkdir -p assets/images/uploads
chmod 777 assets/images/uploads

# Check PHP upload settings
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

### Problem: Newsletter Not Working

**Solution:**

```bash
# Check API file exists
ls -la api/newsletter.php

# Check database table
mysql -u root -p
USE global_export_indonesia;
DESCRIBE newsletter_users;
```

### Problem: Admin Can't Login

**Solution:**

```sql
-- Reset admin password
USE global_export_indonesia;
UPDATE admin_users
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE username = 'admin';
-- Password will be: admin123
```

### Problem: PHP Version Too Old

**Solution:**

```bash
# Ubuntu/Debian
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.0

# Check version
php -v
```

---

## 🔒 Security Checklist

After installation, secure your website:

- [ ] Change default admin password
- [ ] Update database credentials
- [ ] Enable HTTPS (SSL certificate)
- [ ] Set proper file permissions
- [ ] Disable directory listing
- [ ] Enable firewall
- [ ] Regular backups
- [ ] Update PHP and MySQL
- [ ] Monitor error logs

---

## 📞 Support

Jika mengalami masalah:

1. Cek error log: `/var/log/apache2/error.log`
2. Cek PHP error log
3. Review troubleshooting section
4. Contact support: support@globalexportindonesia.com

---

## 🎉 Selamat!

Website Anda sudah siap digunakan!

**Next Steps:**

1. Login ke admin panel
2. Ganti password default
3. Upload produk Anda
4. Customize konten
5. Test semua fitur
6. Deploy ke production

---

**Last Updated:** 2024
**Version:** 1.0.0
