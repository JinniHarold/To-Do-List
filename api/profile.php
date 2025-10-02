<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get user profile data
            $stmt = $pdo->prepare("
                SELECT id, username, email, first_name, last_name, bio, interests, 
                       profile_picture, created_at, updated_at
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                throw new Exception('User not found');
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
            $stats = $statsStmt->fetch();
            
            // Calculate current streak
            $streakStmt = $pdo->prepare("
                SELECT DATE(updated_at) as completion_date
                FROM tasks 
                WHERE user_id = ? AND completed = 1 
                ORDER BY updated_at DESC
            ");
            $streakStmt->execute([$user_id]);
            $completions = $streakStmt->fetchAll();
            
            $currentStreak = 0;
            if (!empty($completions)) {
                $today = new DateTime();
                $lastCompletion = new DateTime($completions[0]['completion_date']);
                
                // Check if there was a completion today or yesterday
                $daysDiff = $today->diff($lastCompletion)->days;
                
                if ($daysDiff <= 1) {
                    $currentStreak = 1;
                    $currentDate = $lastCompletion;
                    
                    // Count consecutive days
                    for ($i = 1; $i < count($completions); $i++) {
                        $prevDate = new DateTime($completions[$i]['completion_date']);
                        $diff = $currentDate->diff($prevDate)->days;
                        
                        if ($diff == 1) {
                            $currentStreak++;
                            $currentDate = $prevDate;
                        } else {
                            break;
                        }
                    }
                }
            }
            
            // Parse interests
            $interests = $user['interests'] ? explode(',', $user['interests']) : [];
            $interests = array_map('trim', $interests);
            
            // Format member since date
            $memberSince = date('F Y', strtotime($user['created_at']));
            
            $response = [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'firstName' => $user['first_name'] ?: '',
                    'lastName' => $user['last_name'] ?: '',
                    'bio' => $user['bio'] ?: '',
                    'interests' => $interests,
                    'profilePicture' => $user['profile_picture'],
                    'memberSince' => $memberSince,
                    'totalTasks' => (int)$stats['total_tasks'],
                    'completedTasks' => (int)$stats['completed_tasks'],
                    'pendingTasks' => (int)$stats['pending_tasks'],
                    'currentStreak' => $currentStreak
                ]
            ];
            
            echo json_encode($response);
            break;
            
        case 'PUT':
            // Update user profile
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                throw new Exception('Invalid JSON data');
            }
            
            $updateFields = [];
            $params = [];
            
            // Handle basic profile fields
            if (isset($input['firstName'])) {
                $updateFields[] = 'first_name = ?';
                $params[] = trim($input['firstName']);
            }
            
            if (isset($input['lastName'])) {
                $updateFields[] = 'last_name = ?';
                $params[] = trim($input['lastName']);
            }
            
            if (isset($input['email'])) {
                // Check if email is already taken by another user
                $emailCheckStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $emailCheckStmt->execute([trim($input['email']), $user_id]);
                if ($emailCheckStmt->fetch()) {
                    throw new Exception('Email address is already in use');
                }
                
                $updateFields[] = 'email = ?';
                $params[] = trim($input['email']);
            }
            
            if (isset($input['bio'])) {
                $updateFields[] = 'bio = ?';
                $params[] = trim($input['bio']);
            }
            
            if (isset($input['interests'])) {
                $interests = is_array($input['interests']) ? $input['interests'] : [];
                $interestsStr = implode(',', array_map('trim', $interests));
                $updateFields[] = 'interests = ?';
                $params[] = $interestsStr;
            }
            
            if (isset($input['profilePicture'])) {
                $updateFields[] = 'profile_picture = ?';
                $params[] = $input['profilePicture'];
            }
            
            if (empty($updateFields)) {
                throw new Exception('No fields to update');
            }
            
            $updateFields[] = 'updated_at = CURRENT_TIMESTAMP';
            $params[] = $user_id;
            
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            break;
            
        case 'POST':
            // Handle password change
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['currentPassword']) || !isset($input['newPassword'])) {
                throw new Exception('Current password and new password are required');
            }
            
            // Get current password hash
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($input['currentPassword'], $user['password'])) {
                throw new Exception('Current password is incorrect');
            }
            
            if (strlen($input['newPassword']) < 6) {
                throw new Exception('New password must be at least 6 characters long');
            }
            
            // Update password
            $newPasswordHash = password_hash($input['newPassword'], PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $updateStmt->execute([$newPasswordHash, $user_id]);
            
            echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>