<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('success' => false, 'message' => 'Invalid request method'));
    exit;
}

$email = isset($_POST['email']) ? sanitize($_POST['email']) : '';

// Validate email
if (empty($email) || !validateEmail($email)) {
    echo json_encode(array('success' => false, 'message' => 'Email tidak valid'));
    exit;
}

try {
    $db = getDB();
    
    // Check if email already exists
    $stmt = $db->prepare("SELECT id, status FROM newsletter_users WHERE email = ?");
    $stmt->execute(array($email));
    $existing = $stmt->fetch();

    if ($existing) {
        if ($existing['status'] === 'active') {
            echo json_encode(array('success' => false, 'message' => 'Email sudah terdaftar'));
            exit;
        } else {
            // Reactivate subscription
            $stmt = $db->prepare("UPDATE newsletter_users SET status = 'active', subscribed_at = NOW(), unsubscribed_at = NULL WHERE email = ?");
            $stmt->execute(array($email));
            echo json_encode(array('success' => true, 'message' => 'Subscription berhasil diaktifkan kembali'));
            exit;
        }
    }

    // Insert new subscriber
    $stmt = $db->prepare("INSERT INTO newsletter_users (email, status) VALUES (?, 'active')");
    $stmt->execute(array($email));
    
    echo json_encode(array('success' => true, 'message' => 'Terima kasih telah berlangganan!'));
    
} catch (Exception $e) {
    echo json_encode(array('success' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.'));
}
?>
