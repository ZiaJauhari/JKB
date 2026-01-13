<?php
/**
 * Helper Functions
 * Global Export Indonesia
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Format price to USD
 */
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

/**
 * Format date
 */
function formatDate($date) {
    return date('d M Y', strtotime($date));
}

/**
 * Get excerpt from text
 */
function getExcerpt($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Check if user is logged in
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if any user is logged in (admin or regular user)
 */
function isLoggedIn() {
    return isAdminLoggedIn() || isUserLoggedIn();
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'];
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return ['type' => $type, 'message' => $message];
    }
    return null;
}

/**
 * Get product category badge color
 */
function getCategoryBadge($category) {
    $badges = [
        'pertanian' => 'success',
        'kerajinan' => 'warning',
        'perikanan' => 'info',
        'lainnya' => 'secondary'
    ];
    return $badges[$category] ?? 'secondary';
}

/**
 * Get status badge color
 */
function getStatusBadge($status) {
    $badges = [
        'new' => 'primary',
        'in_progress' => 'warning',
        'completed' => 'success',
        'cancelled' => 'danger',
        'read' => 'info',
        'replied' => 'success',
        'active' => 'success',
        'unsubscribed' => 'secondary',
        'available' => 'success',
        'limited' => 'warning',
        'out_of_stock' => 'danger'
    ];
    return $badges[$status] ?? 'secondary';
}

/**
 * Upload image file
 */
function uploadImage($file, $targetDir = 'assets/images/uploads/') {
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $targetFile = $targetDir . $newFileName;
    
    // Check if image file is actual image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ['success' => false, 'message' => 'File is not an image.'];
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return ['success' => false, 'message' => 'File is too large. Max 5MB.'];
    }
    
    // Allow certain file formats
    if(!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, PNG, GIF & WEBP files are allowed.'];
    }
    
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return ['success' => true, 'filename' => $newFileName, 'path' => $targetFile];
    } else {
        return ['success' => false, 'message' => 'Error uploading file.'];
    }
}

/**
 * Pagination helper
 */
function paginate($totalItems, $itemsPerPage, $currentPage) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'total_items' => $totalItems,
        'items_per_page' => $itemsPerPage,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'offset' => $offset
    ];
}

/**
 * Send email (basic implementation)
 */
function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Global Export Indonesia <noreply@globalexportindonesia.com>' . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
