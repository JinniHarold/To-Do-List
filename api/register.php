<?php
// Start output buffering and disable all error output
ob_start();
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

require_once '../config/database.php';
require_once '../includes/auth.php';

// Clear any previous output and set JSON header
ob_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['firstName']) || !isset($input['lastName']) || !isset($input['email']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$firstName = trim($input['firstName']);
$lastName = trim($input['lastName']);
$username = $firstName . ' ' . $lastName; // Combine first and last name as username
$email = trim($input['email']);
$password = $input['password'];

// Validate input
if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields are required']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit();
}

try {
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Username or email already exists']);
        exit();
    }
    
    // Hash password and create user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hashedPassword]);
    
    $userId = $pdo->lastInsertId();
    
    // Log in the user
    $user = [
        'id' => $userId,
        'username' => $username,
        'email' => $email
    ];
    login($user);
    
    echo json_encode(['success' => true, 'message' => 'Registration successful']);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>