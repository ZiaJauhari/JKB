<?php
session_start();
require_once '../includes/functions.php';

// Determine redirect URL based on user type
$redirect_url = '../index.php'; // default for admin

if (isUserLoggedIn()) {
    $redirect_url = '../index.php';
}

// Destroy session
session_destroy();

// Redirect
redirect($redirect_url);
?>
