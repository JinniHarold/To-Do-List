<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Include database connection
try {
    require_once '../config/database.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Get user profile data
        $stmt = $pdo->prepare("
            SELECT id, username, email, first_name, last_name, bio, interests, 
                   profile_picture, created_at, updated_at
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'User not found']);
            exit;
        }
        
        // Get user statistics
        $statsStmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_tasks,
                SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_tasks
            FROM tasks 
            WHERE user_id = ?
        ");
        $statsStmt->execute([$user_id]);
        $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
        
        // Calculate current streak (simplified version)
        $currentStreak = 0;
        $streakStmt = $pdo->prepare("
            SELECT COUNT(*) as streak_count
            FROM tasks 
            WHERE user_id = ? AND completed = 1 
            AND DATE(updated_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ");
        $streakStmt->execute([$user_id]);
        $streakData = $streakStmt->fetch(PDO::FETCH_ASSOC);
        $currentStreak = $streakData['streak_count'] ?? 0;
        
        // Prepare response data
        $responseData = [
            'success' => true,
            'user' => [
                'id' => (int)$user['id'],
                'username' => $user['username'] ?? '',
                'email' => $user['email'] ?? '',
                'first_name' => $user['first_name'] ?? '',
                'last_name' => $user['last_name'] ?? '',
                'bio' => $user['bio'] ?? '',
                'interests' => $user['interests'] ?? '',
                'profile_picture' => $user['profile_picture'] ?? '',
                'created_at' => $user['created_at'] ?? '',
                'updated_at' => $user['updated_at'] ?? '',
                'total_tasks' => (int)($stats['total_tasks'] ?? 0),
                'completed_tasks' => (int)($stats['completed_tasks'] ?? 0),
                'pending_tasks' => (int)($stats['pending_tasks'] ?? 0),
                'current_streak' => $currentStreak
            ]
        ];
        
        echo json_encode($responseData);
        
    } elseif ($method === 'PUT') {
        // Update profile data
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
            exit;
        }
        
        $updateFields = [];
        $params = [];
        
        // Handle profile fields
        if (isset($input['first_name'])) {
            $updateFields[] = 'first_name = ?';
            $params[] = trim($input['first_name']);
        }
        
        if (isset($input['last_name'])) {
            $updateFields[] = 'last_name = ?';
            $params[] = trim($input['last_name']);
        }
        
        if (isset($input['email'])) {
            // Check if email is already taken by another user
            $emailCheckStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $emailCheckStmt->execute([trim($input['email']), $user_id]);
            if ($emailCheckStmt->fetch()) {
                http_response_code(409);
                echo json_encode(['success' => false, 'error' => 'Email address is already in use']);
                exit;
            }
            
            $updateFields[] = 'email = ?';
            $params[] = trim($input['email']);
        }
        
        if (isset($input['bio'])) {
            $updateFields[] = 'bio = ?';
            $params[] = trim($input['bio']);
        }
        
        if (isset($input['interests'])) {
            $updateFields[] = 'interests = ?';
            $params[] = trim($input['interests']);
        }
        
        if (isset($input['profile_picture'])) {
            $updateFields[] = 'profile_picture = ?';
            $params[] = $input['profile_picture'];
        }
        
        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No fields to update']);
            exit;
        }
        
        $updateFields[] = 'updated_at = CURRENT_TIMESTAMP';
        $params[] = $user_id;
        
        $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?>