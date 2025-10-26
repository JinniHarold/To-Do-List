<?php
// Start output buffering and disable all error output
ob_start();
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

require_once '../includes/auth.php';

// Clear any previous output and set JSON header
ob_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Simple logout - clear session and destroy
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>