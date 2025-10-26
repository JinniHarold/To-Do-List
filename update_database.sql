-- Database update script to add missing columns to existing users table
USE dailydo;

-- Add missing columns to users table if they don't exist
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS first_name VARCHAR(100),
ADD COLUMN IF NOT EXISTS last_name VARCHAR(100),
ADD COLUMN IF NOT EXISTS bio TEXT,
ADD COLUMN IF NOT EXISTS interests TEXT,
ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255),
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Ensure the tasks table has all required columns
ALTER TABLE tasks 
ADD COLUMN IF NOT EXISTS completed BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Update existing tasks to sync completed field with status
UPDATE tasks SET completed = 1 WHERE status = 'completed';
UPDATE tasks SET completed = 0 WHERE status = 'pending';

-- Add missing columns to tasks table if they don't exist
ALTER TABLE tasks 
ADD COLUMN IF NOT EXISTS reminder BOOLEAN DEFAULT FALSE,
ADD COLUMN IF NOT EXISTS reminder_time INT DEFAULT 15;