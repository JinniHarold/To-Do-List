-- Update database to add role-based access control
USE dailydo;

-- Add role column to users table
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER email;

-- Create an admin user (you can change these credentials)
-- Password is 'admin123' (hashed)
INSERT INTO users (username, email, role, password, first_name, last_name) 
VALUES ('Admin User', 'admin@dailydo.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User')
ON DUPLICATE KEY UPDATE role = 'admin';

-- Update existing users to have 'user' role if not already set
UPDATE users SET role = 'user' WHERE role IS NULL;