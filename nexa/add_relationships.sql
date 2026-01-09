-- ============================================
-- Migration: Add Relationships to Existing Database
-- Description: Menambahkan foreign key constraints ke database yang sudah ada
-- IMPORTANT: Pastikan database dan tabel sudah dibuat dengan create_database.sql terlebih dahulu
-- Run this after backing up your database
-- ============================================

USE global_export_indonesia;

-- Check if database exists
SELECT 'Database exists' as status;

-- Check existing tables
SHOW TABLES;

-- ============================================
-- Step 1: Add new columns for foreign keys
-- ============================================

-- Add product_id to inquiries table
ALTER TABLE inquiries ADD COLUMN product_id INT NULL AFTER product_type;
ALTER TABLE inquiries ADD INDEX idx_product_id (product_id);

-- Add author_id to articles table
ALTER TABLE articles ADD COLUMN author_id INT NULL AFTER author;
ALTER TABLE articles ADD INDEX idx_author_id (author_id);

-- ============================================
-- Step 2: Populate foreign key values from existing data
-- ============================================

-- Update inquiries.product_id based on product_type matching
UPDATE inquiries SET product_id = (
    SELECT p.id FROM products p
    WHERE p.product_name LIKE CONCAT('%', inquiries.product_type, '%')
    OR LOWER(p.category) = LOWER(inquiries.product_type)
    LIMIT 1
) WHERE product_id IS NULL AND product_type IS NOT NULL;

-- Update articles.author_id based on author name matching
UPDATE articles SET author_id = (
    SELECT a.id FROM admin_users a
    WHERE a.full_name = articles.author
    OR a.username = articles.author
    LIMIT 1
) WHERE author_id IS NULL AND author IS NOT NULL;

-- ============================================
-- Step 3: Add foreign key constraints
-- ============================================

-- Add foreign key for inquiries -> products
ALTER TABLE inquiries ADD CONSTRAINT fk_inquiries_product
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL ON UPDATE CASCADE;

-- Add foreign key for articles -> admin_users
ALTER TABLE articles ADD CONSTRAINT fk_articles_author
FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL ON UPDATE CASCADE;

-- ============================================
-- Step 4: Optional - Add more relationships if needed
-- ============================================

-- Example: Add inquiry_id to testimonials (if you want to link testimonials to inquiries)
-- ALTER TABLE testimonials ADD COLUMN inquiry_id INT NULL AFTER id;
-- ALTER TABLE testimonials ADD INDEX idx_inquiry_id (inquiry_id);
-- ALTER TABLE testimonials ADD CONSTRAINT fk_testimonials_inquiry
-- FOREIGN KEY (inquiry_id) REFERENCES inquiries(id) ON DELETE SET NULL;

-- ============================================
-- Verification Queries
-- ============================================

-- Check if foreign keys were added successfully
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

-- Sample JOIN query to test relationships
-- Get inquiries with product details
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
ORDER BY i.created_at DESC;

-- Get articles with author details
SELECT
    a.id,
    a.title,
    a.slug,
    u.full_name as author_name,
    u.email as author_email,
    a.category,
    a.is_published,
    a.created_at
FROM articles a
LEFT JOIN admin_users u ON a.author_id = u.id
ORDER BY a.created_at DESC;

-- ============================================
-- Selesai! Relationships berhasil ditambahkan
-- ============================================
