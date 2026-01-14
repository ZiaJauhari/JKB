<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$page_title = "Kelola Users";

$db = getDB();
$page_title = "Kelola Users";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute(array($id));
        setFlashMessage('success', 'User berhasil dihapus!');
        redirect('users.php');
    } catch (Exception $e) {
        setFlashMessage('danger', 'Gagal menghapus user!');
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
    $where[] = "(username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status !== '') {
    $where[] = "is_active = ?";
    $params[] = $status === '1' ? 1 : 0;
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM users $where_clause";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total_items = $stmt->fetch()['total'];

// Calculate pagination
$pagination = paginate($total_items, $items_per_page, $page);

// Get users
$sql = "SELECT * FROM users $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $db->prepare($sql);
$execute_params = array_merge($params, array($items_per_page, $pagination['offset']));
$stmt->execute($execute_params);
$users = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-users me-2"></i>Kelola Users</h2>
                    <p class="text-muted mb-0">Lihat dan kelola user accounts</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-6">Total: <?php echo $total_items; ?> users</span>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="search" placeholder="Cari username, email, atau nama..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Cari</button>
                        </div>
                        <div class="col-md-2">
                            <a href="users.php" class="btn btn-secondary w-100"><i class="fas fa-redo me-2"></i>Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($users) > 0): ?>
                                    <?php foreach($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td>
                                            <i class="fas fa-envelope me-2"></i>
                                            <?php echo htmlspecialchars($user['email']); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><small><?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Never'; ?></small></td>
                                        <td>
                                            <a href="users.php?delete=<?php echo $user['id']; ?>"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Yakin ingin menghapus user ini?')"
                                               title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Tidak ada user ditemukan</p>
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
                    <p class="text-muted">Export daftar user accounts</p>
                    <button class="btn btn-success" onclick="exportUsers()">
                        <i class="fas fa-file-csv me-2"></i>Export ke CSV
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportUsers() {
    // Get all users
    const users = <?php
        $stmt = $db->query("SELECT username, full_name, email, is_active, last_login FROM users ORDER BY created_at DESC");
        $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($all_users);
    ?>;

    // Create CSV content
    let csvContent = "Username,Full Name,Email,Status,Last Login\n";
    users.forEach(user => {
        const status = user.is_active ? 'Active' : 'Inactive';
        const lastLogin = user.last_login ? new Date(user.last_login).toLocaleDateString() : 'Never';
        csvContent += `"${user.username}","${user.full_name}","${user.email}","${status}","${lastLogin}"\n`;
    });

    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'users_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>

<?php include 'includes/footer.php'; ?>
