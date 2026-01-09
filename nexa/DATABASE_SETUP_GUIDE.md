# Panduan Setup Database dengan Relationships

## Persiapan

Pastikan XAMPP sudah terinstall dan MySQL service sedang berjalan.

## Opsi 1: Menggunakan phpMyAdmin (Direkomendasikan)

### Langkah 1: Buka phpMyAdmin

1. Jalankan XAMPP Control Panel
2. Klik "Start" pada MySQL
3. Klik "Admin" pada MySQL atau buka browser ke `http://localhost/phpmyadmin`

### Langkah 2: Buat Database

1. Klik "New" di sidebar kiri
2. Masukkan nama database: `global_export_indonesia`
3. Pilih collation: `utf8mb4_unicode_ci`
4. Klik "Create"

### Langkah 3: Import Schema Database

1. Klik database `global_export_indonesia` di sidebar
2. Klik tab "Import"
3. Klik "Choose File" dan pilih `updated_create_database.sql`
4. Klik "Go" untuk menjalankan import
5. Tunggu sampai selesai (akan muncul pesan sukses)

### Langkah 4: Import Sample Data (Opsional)

1. Tetap di database `global_export_indonesia`
2. Klik tab "Import"
3. Pilih file `sample_data.sql`
4. Klik "Go"

### Langkah 5: Verifikasi Relationships

1. Klik tab "SQL"
2. Jalankan query berikut untuk melihat foreign keys:

```sql
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_SCHEMA = 'global_export_indonesia'
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME;
```

3. Jalankan sample JOIN query:

```sql
SELECT
    i.id,
    i.full_name,
    i.company_name,
    i.product_type,
    p.product_name,
    p.category,
    p.price,
    i.status,
    i.created_at
FROM inquiries i
LEFT JOIN products p ON i.product_id = p.id
ORDER BY i.created_at DESC
LIMIT 5;
```

## Opsi 2: Menggunakan Command Line (MySQL CLI)

### Langkah 1: Buka Command Prompt

1. Jalankan XAMPP Control Panel
2. Klik "Start" pada MySQL
3. Buka Command Prompt sebagai Administrator
4. Navigate ke folder XAMPP MySQL: `cd C:\xampp\mysql\bin`

### Langkah 2: Login ke MySQL

```
mysql -u root -p
```

(Kosongkan password jika default)

### Langkah 3: Buat dan Setup Database

```sql
-- Buat database
CREATE DATABASE IF NOT EXISTS global_export_indonesia
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Gunakan database
USE global_export_indonesia;

-- Import schema (copy paste isi file updated_create_database.sql)
-- ... paste seluruh isi updated_create_database.sql di sini ...

-- Import sample data (optional)
-- ... paste seluruh isi sample_data.sql di sini ...
```

### Langkah 4: Verifikasi

```sql
-- Lihat tabel yang ada
SHOW TABLES;

-- Lihat foreign keys
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_SCHEMA = 'global_export_indonesia'
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME;

-- Test JOIN
SELECT
    i.id,
    i.full_name,
    i.product_type,
    p.product_name,
    p.price
FROM inquiries i
LEFT JOIN products p ON i.product_id = p.id
LIMIT 5;
```

## Opsi 3: Jika Database Sudah Ada (Migration)

Jika Anda sudah memiliki database dengan data existing:

1. **Backup database** terlebih dahulu!
2. Import `add_relationships.sql` melalui phpMyAdmin atau CLI
3. Script akan otomatis menambahkan kolom dan foreign keys

## Troubleshooting

### Error: Table doesn't exist

- Pastikan sudah menjalankan `create_database.sql` atau `updated_create_database.sql` terlebih dahulu

### Error: Duplicate entry

- Database sudah ada, gunakan `add_relationships.sql` untuk migration

### Error: Access denied

- Pastikan MySQL service sedang berjalan
- Coba login dengan user yang tepat

### Error: Foreign key constraint fails

- Pastikan data di tabel parent (products, admin_users) sudah ada sebelum menambahkan foreign keys

## Testing Relationships

Setelah setup selesai, test dengan query berikut:

### 1. Inquiries dengan Product Details

```sql
SELECT
    i.*,
    p.product_name,
    p.category,
    p.price
FROM inquiries i
LEFT JOIN products p ON i.product_id = p.id;
```

### 2. Articles dengan Author Details

```sql
SELECT
    a.*,
    u.full_name as author_name,
    u.email as author_email
FROM articles a
LEFT JOIN admin_users u ON a.author_id = u.id;
```

### 3. Lihat Structure Database

```sql
-- Lihat semua tabel
SHOW TABLES;

-- Lihat struktur tabel dengan foreign keys
DESCRIBE inquiries;
DESCRIBE articles;
DESCRIBE products;
DESCRIBE admin_users;
```

## File yang Dibuat

- `database_relationships.md` - Dokumentasi relationships
- `updated_create_database.sql` - Schema lengkap dengan relationships
- `add_relationships.sql` - Migration script untuk database existing
- `DATABASE_SETUP_GUIDE.md` - Panduan ini

## Support

Jika mengalami kesulitan, pastikan:

1. XAMPP MySQL service sedang berjalan
2. File SQL tidak corrupt (bisa dibuka dengan text editor)
3. User MySQL memiliki permission yang cukup
