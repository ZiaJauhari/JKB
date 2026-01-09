<?php
require_once 'config/database.php';

$db = getDB();

// Generate new password hash
$new_password = password_hash('admin123', PASSWORD_DEFAULT);

echo "New password hash: " . $new_password . "\n\n";

// Update password
$stmt = $db->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
$result = $stmt->execute(array($new_password));

if ($result) {
    echo "✓ Password updated successfully!\n\n";
    
    // Verify the update
    $stmt = $db->prepare("SELECT username, password FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    echo "Verifying password...\n";
    if (password_verify('admin123', $user['password'])) {
        echo "✓ Password verification successful!\n";
        echo "\nLogin credentials:\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    } else {
        echo "✗ Password verification failed!\n";
    }
} else {
    echo "✗ Failed to update password\n";
}
?>
