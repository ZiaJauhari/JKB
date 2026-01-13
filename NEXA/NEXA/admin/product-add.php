<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Tambah Produk";
$errors = array();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = sanitize($_POST['product_name']);
    $category = sanitize($_POST['category']);
    $description = sanitize($_POST['description']);
    $price = floatval($_POST['price']);
    $unit = sanitize($_POST['unit']);
    $min_order = intval($_POST['min_order']);
    $image_url = sanitize($_POST['image_url']);
    $stock_status = sanitize($_POST['stock_status']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validation
    if (empty($product_name)) {
        $errors[] = "Nama produk harus diisi";
    }
    if (empty($category)) {
        $errors[] = "Kategori harus dipilih";
    }
    if ($price <= 0) {
        $errors[] = "Harga harus lebih dari 0";
    }
    if ($min_order <= 0) {
        $errors[] = "Minimum order harus lebih dari 0";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO products (product_name, category, description, price, unit, min_order, image_url, stock_status, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array($product_name, $category, $description, $price, $unit, $min_order, $image_url, $stock_status, $is_featured, $is_active));
            
            setFlashMessage('success', 'Produk berhasil ditambahkan!');
            redirect('products.php');
        } catch (Exception $e) {
            $errors[] = "Gagal menambahkan produk: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-plus-circle me-2"></i>Tambah Produk Baru</h2>
                    <p class="text-muted mb-0">Tambahkan produk ekspor baru</p>
                </div>
                <a href="products.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            
            <?php if (count($errors) > 0): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Form -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" required
                                           value="<?php echo isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="5"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="price" class="form-label">Harga (USD) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" id="price" name="price" required
                                               value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>">
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="unit" class="form-label">Satuan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="unit" name="unit" required>
                                            <option value="kg" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'kg') ? 'selected' : ''; ?>>kg</option>
                                            <option value="ton" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'ton') ? 'selected' : ''; ?>>ton</option>
                                            <option value="pcs" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'pcs') ? 'selected' : ''; ?>>pcs</option>
                                            <option value="set" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'set') ? 'selected' : ''; ?>>set</option>
                                            <option value="box" <?php echo (isset($_POST['unit']) && $_POST['unit'] == 'box') ? 'selected' : ''; ?>>box</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="min_order" class="form-label">Min. Order <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="min_order" name="min_order" required
                                               value="<?php echo isset($_POST['min_order']) ? $_POST['min_order'] : '1'; ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image_url" class="form-label">URL Gambar</label>
                                    <input type="url" class="form-control" id="image_url" name="image_url"
                                           value="<?php echo isset($_POST['image_url']) ? htmlspecialchars($_POST['image_url']) : 'https://images.unsplash.com/photo-1560493676-04071c5f467b?w=800'; ?>">
                                    <small class="text-muted">Gunakan URL gambar dari Unsplash atau sumber lain</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="pertanian" <?php echo (isset($_POST['category']) && $_POST['category'] == 'pertanian') ? 'selected' : ''; ?>>Pertanian</option>
                                        <option value="kerajinan" <?php echo (isset($_POST['category']) && $_POST['category'] == 'kerajinan') ? 'selected' : ''; ?>>Kerajinan</option>
                                        <option value="perikanan" <?php echo (isset($_POST['category']) && $_POST['category'] == 'perikanan') ? 'selected' : ''; ?>>Perikanan</option>
                                        <option value="lainnya" <?php echo (isset($_POST['category']) && $_POST['category'] == 'lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="stock_status" class="form-label">Status Stok</label>
                                    <select class="form-select" id="stock_status" name="stock_status">
                                        <option value="available" <?php echo (isset($_POST['stock_status']) && $_POST['stock_status'] == 'available') ? 'selected' : 'selected'; ?>>Available</option>
                                        <option value="limited" <?php echo (isset($_POST['stock_status']) && $_POST['stock_status'] == 'limited') ? 'selected' : ''; ?>>Limited</option>
                                        <option value="out_of_stock" <?php echo (isset($_POST['stock_status']) && $_POST['stock_status'] == 'out_of_stock') ? 'selected' : ''; ?>>Out of Stock</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                               <?php echo (isset($_POST['is_featured']) && $_POST['is_featured']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_featured">
                                            Produk Unggulan
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="products.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
