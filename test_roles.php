<?php
require_once 'includes/auth.php';
require_once 'config/database.php';

echo "<h2>Role-Based Access Control Test</h2>";

// Test 1: Check if admin user exists
try {
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE role = 'admin'");
    $stmt->execute();
    $adminUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Test 1: Admin Users in Database</h3>";
    if (count($adminUsers) > 0) {
        echo "<p style='color: green;'>✓ Admin users found:</p>";
        foreach ($adminUsers as $admin) {
            echo "<ul>";
            echo "<li>ID: " . $admin['id'] . "</li>";
            echo "<li>Username: " . $admin['username'] . "</li>";
            echo "<li>Email: " . $admin['email'] . "</li>";
            echo "<li>Role: " . $admin['role'] . "</li>";
            echo "</ul><hr>";
        }
    } else {
        echo "<p style='color: red;'>✗ No admin users found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test 2: Check if regular users exist
try {
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE role = 'user' LIMIT 5");
    $stmt->execute();
    $regularUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Test 2: Regular Users in Database</h3>";
    if (count($regularUsers) > 0) {
        echo "<p style='color: green;'>✓ Regular users found:</p>";
        foreach ($regularUsers as $user) {
            echo "<ul>";
            echo "<li>ID: " . $user['id'] . "</li>";
            echo "<li>Username: " . $user['username'] . "</li>";
            echo "<li>Email: " . $user['email'] . "</li>";
            echo "<li>Role: " . $user['role'] . "</li>";
            echo "</ul><hr>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ No regular users found (this is normal if no users have registered yet)</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test 3: Check authentication functions
echo "<h3>Test 3: Authentication Functions</h3>";

if (function_exists('isAdmin')) {
    echo "<p style='color: green;'>✓ isAdmin() function exists</p>";
} else {
    echo "<p style='color: red;'>✗ isAdmin() function missing</p>";
}

if (function_exists('requireAdmin')) {
    echo "<p style='color: green;'>✓ requireAdmin() function exists</p>";
} else {
    echo "<p style='color: red;'>✗ requireAdmin() function missing</p>";
}

if (function_exists('getUserRole')) {
    echo "<p style='color: green;'>✓ getUserRole() function exists</p>";
} else {
    echo "<p style='color: red;'>✗ getUserRole() function missing</p>";
}

// Test 4: Session status
echo "<h3>Test 4: Current Session Status</h3>";
if (isLoggedIn()) {
    $currentUser = getCurrentUser();
    echo "<p style='color: green;'>✓ User is logged in:</p>";
    echo "<ul>";
    echo "<li>Username: " . $currentUser['username'] . "</li>";
    echo "<li>Email: " . $currentUser['email'] . "</li>";
    echo "<li>Role: " . $currentUser['role'] . "</li>";
    echo "<li>Is Admin: " . (isAdmin() ? 'Yes' : 'No') . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color: orange;'>⚠ No user is currently logged in</p>";
}

echo "<h3>Test Instructions</h3>";
echo "<p>To fully test the role-based access control:</p>";
echo "<ol>";
echo "<li><strong>Login as Admin:</strong> Use email 'admin@dailydo.com' and password 'admin123'</li>";
echo "<li><strong>Test Admin Access:</strong> Navigate to <a href='admin.php'>admin.php</a> - should work</li>";
echo "<li><strong>Register as Regular User:</strong> Create a new account via registration</li>";
echo "<li><strong>Test User Access:</strong> Try to access <a href='admin.php'>admin.php</a> - should redirect to dashboard</li>";
echo "<li><strong>Check Navigation:</strong> Admin users should see 'User Management' in sidebar, regular users should not</li>";
echo "</ol>";

echo "<p><a href='login.php'>Go to Login Page</a> | <a href='register.php'>Go to Registration Page</a></p>";
?>