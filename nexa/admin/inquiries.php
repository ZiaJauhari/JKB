<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Kelola Inquiry";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM inquiries WHERE id = ?");
        $stmt->execute(array($id));
        setFlashMessage('success', 'Inquiry berhasil dihapus!');
        redirect('inquiries.php');
    } catch (Exception $e) {
        setFlashMessage('danger', 'Gagal menghapus inquiry!');
    }
}

// Handle Status Update
if (isset($_GET['update_status'])) {
    $id = (int)$_GET['update_status'];
    $status = sanitize($_GET['status']);
    try {
        $stmt = $db->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->execute(array($status, $id));
        setFlashMessage('success', 'Status inquiry berhasil diupdate!');
        redirect('inquiries.php');
    } catch (Exception $e) {
        setFlashMessage('danger', 'Gagal mengupdate status!');
    }
}

// Get filter parameters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;

// Build query
$where = array();
$params = array();

if ($search) {
    $where[] = "(full_name LIKE ? OR company_name LIKE ? OR email LIKE ? OR message LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status) {
    $where[] = "status = ?";
    $params[] = $status;
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM inquiries $where_clause";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetch()['total'];

// Calculate pagination
$pagination = paginate($total_items, $items_per_page, $page);

// Get inquiries
$sql = "SELECT * FROM inquiries $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
$execute_params = array_merge($params, array($items_per_page, $pagination['offset']));
$stmt->execute($execute_params);
$inquiries = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-envelope me-2"></i>Kelola Inquiry</h2>
                    <p class="text-muted mb-0">Lihat dan kelola inquiry dari customer</p>
                </div>
            </div>
            
            <!-- Filter & Search -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Cari inquiry..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="new" <?php echo $status == 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="in_progress" <?php echo $status == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo $status == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="inquiries.php" class="btn btn-secondary w-100">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Inquiries Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Nama / Perusahaan</th>
                                    <th>Kontak</th>
                                    <th>Produk</th>
                                    <th>Pesan</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($inquiries) > 0): ?>
                                    <?php foreach($inquiries as $inquiry): ?>
                                    <tr>
                                        <td><?php echo $inquiry['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($inquiry['full_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($inquiry['company_name']); ?></small>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($inquiry['email']); ?><br>
                                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($inquiry['phone']); ?>
                                            </small>
                                        </td>
                                        <td><?php echo htmlspecialchars($inquiry['product_type']); ?></td>
                                        <td>
                                            <small><?php echo getExcerpt($inquiry['message'], 50); ?></small>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-<?php echo getStatusBadge($inquiry['status']); ?> dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <?php echo ucfirst(str_replace('_', ' ', $inquiry['status'])); ?>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="?update_status=<?php echo $inquiry['id']; ?>&status=new">New</a></li>
                                                    <li><a class="dropdown-item" href="?update_status=<?php echo $inquiry['id']; ?>&status=in_progress">In Progress</a></li>
                                                    <li><a class="dropdown-item" href="?update_status=<?php echo $inquiry['id']; ?>&status=completed">Completed</a></li>
                                                    <li><a class="dropdown-item" href="?update_status=<?php echo $inquiry['id']; ?>&status=cancelled">Cancelled</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td><small><?php echo formatDate($inquiry['created_at']); ?></small></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewModal<?php echo $inquiry['id']; ?>"
                                                    title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="inquiries.php?delete=<?php echo $inquiry['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Yakin ingin menghapus inquiry ini?')"
                                               title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal<?php echo $inquiry['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Inquiry #<?php echo $inquiry['id']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <strong>Nama Lengkap:</strong><br>
                                                            <?php echo htmlspecialchars($inquiry['full_name']); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Nama Perusahaan:</strong><br>
                                                            <?php echo htmlspecialchars($inquiry['company_name']); ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <strong>Email:</strong><br>
                                                            <a href="mailto:<?php echo $inquiry['email']; ?>"><?php echo htmlspecialchars($inquiry['email']); ?></a>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Telepon:</strong><br>
                                                            <a href="tel:<?php echo $inquiry['phone']; ?>"><?php echo htmlspecialchars($inquiry['phone']); ?></a>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Jenis Produk:</strong><br>
                                                        <?php echo htmlspecialchars($inquiry['product_type']); ?>
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Pesan:</strong><br>
                                                        <?php echo nl2br(htmlspecialchars($inquiry['message'])); ?>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Status:</strong><br>
                                                            <span class="badge bg-<?php echo getStatusBadge($inquiry['status']); ?>">
                                                                <?php echo ucfirst(str_replace('_', ' ', $inquiry['status'])); ?>
                                                            </span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Tanggal:</strong><br>
                                                            <?php echo formatDate($inquiry['created_at']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Tidak ada inquiry ditemukan</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if($pagination['total_pages'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status ? '&status=' . $status : ''; ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status ? '&status=' . $status : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $pagination['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $status ? '&status=' . $status : ''; ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
