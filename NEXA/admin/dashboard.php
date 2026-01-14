<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Set defaults for session variables if not set
$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Administrator';
$admin_role = isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'admin';

$db = getDB();

// Get statistics
$stats = array();

// Total products
$stmt = $db->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
$stats['products'] = $stmt->fetch()['total'];

// Total inquiries
$stmt = $db->query("SELECT COUNT(*) as total FROM inquiries");
$stats['inquiries'] = $stmt->fetch()['total'];

// New inquiries (this month)
$stmt = $db->query("SELECT COUNT(*) as total FROM inquiries WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stats['new_inquiries'] = $stmt->fetch()['total'];

// Total users
$stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
$stats['users'] = $stmt->fetch()['total'];

// Total articles
$stmt = $db->query("SELECT COUNT(*) as total FROM articles WHERE is_published = 1");
$stats['articles'] = $stmt->fetch()['total'];

// Total testimonials
$stmt = $db->query("SELECT COUNT(*) as total FROM testimonials WHERE is_active = 1");
$stats['testimonials'] = $stmt->fetch()['total'];

// Recent inquiries
$stmt = $db->query("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5");
$recent_inquiries = $stmt->fetchAll();

// Recent users
$stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
$recent_users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd 0%, #198754 100%);
            color: white;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-globe-asia me-2"></i>NEXA Admin
                    </h4>
                    
                    <div class="text-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=fff&color=0d6efd"
                             alt="Admin" class="rounded-circle mb-2" width="80">
                        <h6 class="mb-0"><?php echo htmlspecialchars($admin_name); ?></h6>
                        <small class="text-white-50"><?php echo ucfirst($admin_role); ?></small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-box me-2"></i>Produk
                        </a>
                        <a class="nav-link" href="inquiries.php">
                            <i class="fas fa-envelope me-2"></i>Inquiry
                        </a>
                        <a class="nav-link" href="articles.php">
                            <i class="fas fa-newspaper me-2"></i>Artikel
                        </a>
                        <a class="nav-link" href="testimonials.php">
                            <i class="fas fa-star me-2"></i>Testimoni
                        </a>
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <hr class="border-light">
                        <a class="nav-link" href="../index.php" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>Lihat Website
                        </a>
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>
            
            <div class="col-md-9 col-lg-10 px-4 py-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2>Dashboard</h2>
                        <p class="text-muted mb-0">Selamat datang kembali, <?php echo htmlspecialchars($admin_name); ?>!</p>
                    </div>
                    <div>
                        <span class="text-muted">
                            <i class="far fa-calendar me-2"></i><?php echo date('d F Y'); ?>
                        </span>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Total Produk</h6>
                                        <h3 class="mb-0"><?php echo $stats['products']; ?></h3>
                                    </div>
                                    <div class="stat-icon bg-primary text-white">
                                        <i class="fas fa-box"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Total Inquiry</h6>
                                        <h3 class="mb-0"><?php echo $stats['inquiries']; ?></h3>
                                        <small class="text-success">+<?php echo $stats['new_inquiries']; ?> bulan ini</small>
                                    </div>
                                    <div class="stat-icon bg-success text-white">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Users</h6>
                                        <h3 class="mb-0"><?php echo $stats['users']; ?></h3>
                                    </div>
                                    <div class="stat-icon bg-info text-white">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Artikel</h6>
                                        <h3 class="mb-0"><?php echo $stats['articles']; ?></h3>
                                    </div>
                                    <div class="stat-icon bg-warning text-white">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Testimoni</h6>
                                        <h3 class="mb-0"><?php echo $stats['testimonials']; ?></h3>
                                    </div>
                                    <div class="stat-icon bg-danger text-white">
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Data -->
                <div class="row">
                    <!-- Recent Inquiries -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-envelope me-2"></i>Inquiry Terbaru
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($recent_inquiries as $inquiry): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($inquiry['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo getStatusBadge($inquiry['status']); ?>">
                                                        <?php echo $inquiry['status']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo formatDate($inquiry['created_at']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Users -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-users me-2"></i>User Terbaru
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Last Login</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($recent_users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td><?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Never'; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
