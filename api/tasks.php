<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

$user = getCurrentUser();
$userId = $user['id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all tasks for the user
            $search = $_GET['search'] ?? '';
            $priority = $_GET['priority'] ?? '';
            $status = $_GET['status'] ?? '';
            
            $sql = "SELECT * FROM tasks WHERE user_id = ?";
            $params = [$userId];
            
            // Add search filter
            if (!empty($search)) {
                $sql .= " AND (title LIKE ? OR description LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            // Add priority filter
            if (!empty($priority)) {
                $sql .= " AND priority = ?";
                $params[] = $priority;
            }
            
            // Add status filter
            if (!empty($status)) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY 
                CASE 
                    WHEN status = 'pending' THEN 0 
                    ELSE 1 
                END,
                CASE 
                    WHEN deadline IS NOT NULL AND deadline < NOW() AND status = 'pending' THEN 0
                    WHEN deadline IS NOT NULL THEN 1
                    ELSE 2
                END,
                CASE priority 
                    WHEN 'high' THEN 3 
                    WHEN 'medium' THEN 2 
                    WHEN 'low' THEN 1 
                END DESC,
                deadline ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $tasks = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'tasks' => $tasks]);
            break;
            
        case 'POST':
            // Create new task
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['title'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Title is required']);
                exit();
            }
            
            $sql = "INSERT INTO tasks (user_id, title, description, priority, deadline, reminder, reminder_time, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $userId,
                $input['title'],
                $input['description'] ?? '',
                $input['priority'] ?? 'medium',
                !empty($input['deadline']) ? $input['deadline'] : null,
                isset($input['reminder']) ? (bool)$input['reminder'] : false,
                $input['reminder_time'] ?? 15,
                $input['status'] ?? 'pending'
            ]);
            
            $taskId = $pdo->lastInsertId();
            
            // Fetch the created task
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->execute([$taskId]);
            $task = $stmt->fetch();
            
            echo json_encode(['success' => true, 'task' => $task]);
            break;
            
        case 'PUT':
            // Update existing task
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Task ID is required']);
                exit();
            }
            
            // Check if task belongs to user
            $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$input['id'], $userId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['error' => 'Task not found']);
                exit();
            }
            
            $sql = "UPDATE tasks SET 
                    title = ?, 
                    description = ?, 
                    priority = ?, 
                    deadline = ?, 
                    reminder = ?, 
                    reminder_time = ?, 
                    status = ?,
                    completed = ?
                    WHERE id = ? AND user_id = ?";
            
            $completed = ($input['status'] ?? 'pending') === 'completed';
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $input['title'],
                $input['description'] ?? '',
                $input['priority'] ?? 'medium',
                !empty($input['deadline']) ? $input['deadline'] : null,
                isset($input['reminder']) ? (bool)$input['reminder'] : false,
                $input['reminder_time'] ?? 15,
                $input['status'] ?? 'pending',
                $completed,
                $input['id'],
                $userId
            ]);
            
            // Fetch the updated task
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->execute([$input['id']]);
            $task = $stmt->fetch();
            
            echo json_encode(['success' => true, 'task' => $task]);
            break;
            
        case 'DELETE':
            // Delete task
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Task ID is required']);
                exit();
            }
            
            // Check if task belongs to user
            $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$input['id'], $userId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['error' => 'Task not found']);
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$input['id'], $userId]);
            
            echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error: ' . $e->getMessage()]);
}
?>