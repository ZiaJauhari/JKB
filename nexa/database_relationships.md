# Database Relationships - Global Export Indonesia

## Current Database Schema

Database: `global_export_indonesia`

### Tables Overview

1. **inquiries** - Menyimpan data inquiry/konsultasi ekspor
2. **newsletter_subscribers** - Menyimpan email subscribers newsletter
3. **products** - Menyimpan data produk ekspor
4. **articles** - Menyimpan artikel/blog tentang ekspor
5. **testimonials** - Menyimpan testimoni dari klien
6. **contact_messages** - Menyimpan pesan dari form kontak umum
7. **admin_users** - Menyimpan data admin untuk login

## Current Relationships

**Tidak ada foreign key constraints yang didefinisikan dalam schema saat ini.** Semua tabel berdiri sendiri tanpa hubungan relational yang eksplisit.

### Entity-Relationship Diagram (Text-based)

```
+-------------------+     +-----------------------+
|   admin_users     |     |  newsletter_subscribers |
+-------------------+     +-----------------------+
| id (PK)           |     | id (PK)               |
| username          |     | email                 |
| password          |     | status                |
| full_name         |     | subscribed_at         |
| email             |     | unsubscribed_at       |
| role              |     +-----------------------+
| is_active         |
| last_login        |
| created_at        |
| updated_at        |
+-------------------+

+-------------------+     +-----------------------+
|    inquiries      |     |     products          |
+-------------------+     +-----------------------+
| id (PK)           |     | id (PK)               |
| full_name         |     | product_name          |
| company_name      |     | category              |
| email             |     | description           |
| phone             |     | price                 |
| product_type      |     | unit                  |
| message           |     | min_order             |
| status            |     | image_url             |
| created_at        |     | stock_status          |
| updated_at        |     | is_featured           |
+-------------------+     | is_active             |
                           | created_at            |
                           | updated_at            |
                           +-----------------------+

+-------------------+     +-----------------------+
|    articles       |     |   testimonials        |
+-------------------+     +-----------------------+
| id (PK)           |     | id (PK)               |
| title             |     | client_name           |
| slug              |     | company_name          |
| excerpt           |     | position              |
| content           |     | testimonial           |
| author            |     | rating                |
| image_url         |     | image_url             |
| category          |     | is_featured           |
| tags              |     | is_active             |
| views             |     | created_at            |
| is_published      |     | updated_at            |
| published_at      |     +-----------------------+
| created_at        |
| updated_at        |
+-------------------+

+-------------------+
| contact_messages  |
+-------------------+
| id (PK)           |
| name              |
| email             |
| subject           |
| message           |
| status            |
| created_at        |
+-------------------+
```

## Proposed Relationships

Untuk membuat database lebih relational dan efisien, berikut adalah relationship yang direkomendasikan:

### 1. Inquiries -> Products

- Tambahkan kolom `product_id` (INT, nullable) di tabel `inquiries`
- Foreign Key: `inquiries.product_id` -> `products.id`
- Mengganti `product_type` (VARCHAR) dengan reference ke products

### 2. Articles -> Admin Users

- Tambahkan kolom `author_id` (INT) di tabel `articles`
- Foreign Key: `articles.author_id` -> `admin_users.id`
- Mengganti kolom `author` (VARCHAR) dengan reference ke admin_users

### 3. Testimonials -> Inquiries (Optional)

- Tambahkan kolom `inquiry_id` (INT, nullable) di tabel `testimonials`
- Foreign Key: `testimonials.inquiry_id` -> `inquiries.id`
- Untuk link testimoni ke inquiry yang berhasil

### 4. Products -> Categories (Optional Enhancement)

- Buat tabel baru `product_categories` jika diperlukan
- Foreign Key: `products.category_id` -> `product_categories.id`

## SQL untuk Menambahkan Relationships

```sql
-- Tambahkan foreign key untuk inquiries -> products
ALTER TABLE inquiries ADD COLUMN product_id INT NULL AFTER product_type;
ALTER TABLE inquiries ADD CONSTRAINT fk_inquiries_product FOREIGN KEY (product_id) REFERENCES products(id);

-- Tambahkan foreign key untuk articles -> admin_users
ALTER TABLE articles ADD COLUMN author_id INT NULL AFTER author;
ALTER TABLE articles ADD CONSTRAINT fk_articles_author FOREIGN KEY (author_id) REFERENCES admin_users(id);

-- Update data existing (contoh mapping)
UPDATE inquiries SET product_id = (SELECT id FROM products WHERE product_name LIKE CONCAT('%', product_type, '%') LIMIT 1);
UPDATE articles SET author_id = (SELECT id FROM admin_users WHERE full_name = author LIMIT 1);
```

## Keuntungan Menambahkan Relationships

1. **Data Integrity**: Mencegah data yang tidak konsisten
2. **Query Efficiency**: Join queries lebih efisien
3. **Maintenance**: Lebih mudah mengupdate data terkait
4. **Analytics**: Lebih mudah membuat report dengan relationship

## Rekomendasi Implementasi

1. Backup database sebelum melakukan perubahan
2. Lakukan perubahan pada development environment terlebih dahulu
3. Update aplikasi PHP untuk menggunakan foreign key baru
4. Test thoroughly sebelum deploy ke production
