<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Not authenticated']));
}

$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method == 'GET') {
        // Get tasks with optional search and filter
        $whereClause = "user_id = ?";
        $params = [$userId];
        
        // Handle search parameter
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $searchTerm = '%' . trim($_GET['search']) . '%';
            $whereClause .= " AND (title LIKE ? OR description LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Handle priority filter
        if (isset($_GET['priority']) && !empty($_GET['priority'])) {
            $whereClause .= " AND priority = ?";
            $params[] = $_GET['priority'];
        }
        
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE $whereClause ORDER BY id DESC");
        $stmt->execute($params);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'tasks' => $tasks]);
        
    } elseif ($method == 'POST') {
        // Create task
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['title']) || empty(trim($input['title']))) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Task title is required']);
            exit;
        }
        
        // Handle deadline - convert to MySQL datetime format if provided
        $deadline = null;
        if (isset($input['deadline']) && !empty($input['deadline'])) {
            try {
                $deadline = date('Y-m-d H:i:s', strtotime($input['deadline']));
            } catch (Exception $e) {
                $deadline = null;
            }
        }
        
        // Handle reminder settings
        $reminder = isset($input['reminder']) ? (bool)$input['reminder'] : false;
        $reminderTime = isset($input['reminder_time']) ? (int)$input['reminder_time'] : 15;
        
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, priority, deadline, reminder, reminder_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $userId, 
            trim($input['title']), 
            $input['description'] ?? '', 
            $input['priority'] ?? 'medium',
            $deadline,
            $reminder,
            $reminderTime
        ]);
        
        $taskId = $pdo->lastInsertId();
        echo json_encode([
            'success' => true, 
            'message' => 'Task created successfully',
            'task_id' => $taskId
        ]);
        
    } elseif ($method == 'PUT') {
        // Update task
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Task ID is required']);
            exit;
        }
        
        // Build dynamic update query
        $updateFields = [];
        $params = [];
        
        if (isset($input['title'])) {
            $updateFields[] = 'title = ?';
            $params[] = trim($input['title']);
        }
        
        if (isset($input['description'])) {
            $updateFields[] = 'description = ?';
            $params[] = $input['description'];
        }
        
        if (isset($input['priority'])) {
            $updateFields[] = 'priority = ?';
            $params[] = $input['priority'];
        }
        
        if (isset($input['deadline'])) {
            $deadline = null;
            if (!empty($input['deadline'])) {
                try {
                    $deadline = date('Y-m-d H:i:s', strtotime($input['deadline']));
                } catch (Exception $e) {
                    $deadline = null;
                }
            }
            $updateFields[] = 'deadline = ?';
            $params[] = $deadline;
        }
        
        if (isset($input['reminder'])) {
            $updateFields[] = 'reminder = ?';
            $params[] = (bool)$input['reminder'];
        }
        
        if (isset($input['reminder_time'])) {
            $updateFields[] = 'reminder_time = ?';
            $params[] = (int)$input['reminder_time'];
        }
        
        if (isset($input['status'])) {
            $updateFields[] = 'status = ?';
            $params[] = $input['status'];
            
            $updateFields[] = 'completed = ?';
            $params[] = ($input['status'] === 'completed') ? 1 : 0;
        }
        
        if (empty($updateFields)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No fields to update']);
            exit;
        }
        
        // Always update the timestamp
        $updateFields[] = 'updated_at = CURRENT_TIMESTAMP';
        $params[] = $input['id'];
        $params[] = $userId;
        
        $sql = "UPDATE tasks SET " . implode(', ', $updateFields) . " WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Task not found or no changes made']);
        }
        
    } elseif ($method == 'DELETE') {
        // Delete task
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Task ID is required']);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$input['id'], $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Task not found']);
        }
        
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>