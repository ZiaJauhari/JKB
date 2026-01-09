<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Kelola Subscribers";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
        $stmt->execute(array($id));
        setFlashMessage('success', 'Subscriber berhasil dihapus!');
        redirect('subscribers.php');
    } catch (Exception $e) {
        setFlashMessage('danger', 'Gagal menghapus subscriber!');
    }
}

// Get filter parameters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 20;

// Build query
$where = array();
$params = array();

if ($search) {
    $where[] = "email LIKE ?";
    $params[] = "%$search%";
}

if ($status) {
    $where[] = "status = ?";
    $params[] = $status;
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM newsletter_subscribers $where_clause";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetch()['total'];

// Calculate pagination
$pagination = paginate($total_items, $items_per_page, $page);

// Get subscribers
$sql = "SELECT * FROM newsletter_subscribers $where_clause ORDER BY subscribed_at DESC LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
$execute_params = array_merge($params, array($items_per_page, $pagination['offset']));
$stmt->execute($execute_params);
$subscribers = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-users me-2"></i>Kelola Subscribers</h2>
                    <p class="text-muted mb-0">Lihat dan kelola newsletter subscribers</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6">Total: <?php echo $total_items; ?> subscribers</span>
                </div>
            </div>
            
            <!-- Filter & Search -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="search" placeholder="Cari email..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="active" <?php echo $status == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="unsubscribed" <?php echo $status == 'unsubscribed' ? 'selected' : ''; ?>>Unsubscribed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Cari</button>
                        </div>
                        <div class="col-md-2">
                            <a href="subscribers.php" class="btn btn-secondary w-100"><i class="fas fa-redo me-2"></i>Reset</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Subscribers Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Tanggal Subscribe</th>
                                    <th>Tanggal Unsubscribe</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($subscribers) > 0): ?>
                                    <?php foreach($subscribers as $subscriber): ?>
                                    <tr>
                                        <td><?php echo $subscriber['id']; ?></td>
                                        <td>
                                            <i class="fas fa-envelope me-2"></i>
                                            <?php echo htmlspecialchars($subscriber['email']); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo getStatusBadge($subscriber['status']); ?>">
                                                <?php echo ucfirst($subscriber['status']); ?>
                                            </span>
                                        </td>
                                        <td><small><?php echo formatDate($subscriber['subscribed_at']); ?></small></td>
                                        <td>
                                            <small>
                                                <?php echo $subscriber['unsubscribed_at'] ? formatDate($subscriber['unsubscribed_at']) : '-'; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <a href="subscribers.php?delete=<?php echo $subscriber['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Yakin ingin menghapus subscriber ini?')"
                                               title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Tidak ada subscriber ditemukan</p>
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
            
            <!-- Export Section -->
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-download me-2"></i>Export Data</h5>
                    <p class="text-muted">Export daftar email subscribers untuk campaign</p>
                    <button class="btn btn-success" onclick="exportEmails()">
                        <i class="fas fa-file-csv me-2"></i>Export ke CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportEmails() {
    // Get all active emails
    const emails = <?php 
        $stmt = $db->query("SELECT email FROM newsletter_subscribers WHERE status = 'active'");
        $active_emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode($active_emails);
    ?>;
    
    // Create CSV content
    let csvContent = "Email\n";
    emails.forEach(email => {
        csvContent += email + "\n";
    });
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'subscribers_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>

<?php include 'includes/footer.php'; ?>
