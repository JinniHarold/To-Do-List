<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

// Set content type to JSON
header('Content-Type: application/json');

// Require admin access
requireAdmin();

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetRequest();
        break;
    case 'PUT':
        handlePutRequest();
        break;
    case 'DELETE':
        handleDeleteRequest();
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

function handleGetRequest() {
    global $pdo;
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'users':
            getAllUsers();
            break;
        case 'stats':
            getUserStats();
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}

function getAllUsers() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                id,
                username,
                email,
                created_at,
                'Active' as status
            FROM users 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process users to extract first_name and last_name from username
        $processedUsers = array_map(function($user) {
            // Try to split username into first and last name
            $nameParts = explode(' ', $user['username'], 2);
            $firstName = $nameParts[0] ?? $user['username'];
            $lastName = $nameParts[1] ?? '';
            
            // If no space in username, use first part of email as fallback
            if (empty($lastName)) {
                $emailParts = explode('@', $user['email']);
                $firstName = $nameParts[0];
                $lastName = '';
            }
            
            return [
                'id' => (int)$user['id'],
                'firstName' => $firstName,
                'lastName' => $lastName,
                'username' => $user['username'],
                'email' => $user['email'],
                'registrationDate' => date('Y-m-d', strtotime($user['created_at'])),
                'status' => $user['status']
            ];
        }, $users);
        
        echo json_encode([
            'success' => true,
            'users' => $processedUsers
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error occurred'
        ]);
    }
}

function getUserStats() {
    global $pdo;
    
    try {
        // Get total users
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users");
        $stmt->execute();
        $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get active users (all users are considered active for now)
        $activeUsers = $totalUsers;
        
        // Get new users this month
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as new_users 
            FROM users 
            WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(created_at) = YEAR(CURRENT_DATE())
        ");
        $stmt->execute();
        $newUsersThisMonth = $stmt->fetch(PDO::FETCH_ASSOC)['new_users'];
        
        echo json_encode([
            'success' => true,
            'stats' => [
                'totalUsers' => (int)$totalUsers,
                'activeUsers' => (int)$activeUsers,
                'newUsers' => (int)$newUsersThisMonth
            ]
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database error occurred'
        ]);
    }
}

function handlePutRequest() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        return;
    }
    
    $userId = (int)$input['id'];
    $username = trim($input['username'] ?? '');
    $email = trim($input['email'] ?? '');
    
    // Validate input
    if (empty($username) || empty($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username and email are required']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
    
    try {
        // Check if email is already taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email is already taken by another user']);
            return;
        }
        
        // Update user
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
}

function handleDeleteRequest() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID is required']);
        return;
    }
    
    $userId = (int)$input['id'];
    $currentUserId = $_SESSION['user_id'];
    
    // Prevent admin from deleting themselves
    if ($userId === $currentUserId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
        return;
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Delete user's tasks first (if tasks table exists)
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        // Delete user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        if ($stmt->rowCount() > 0) {
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            $pdo->rollback();
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found']);
        }
        
    } catch (PDOException $e) {
        $pdo->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
}
?>