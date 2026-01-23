-- Add profile_image column to users table
ALTER TABLE users 
ADD COLUMN profile_image VARCHAR(255) NULL AFTER phone;

-- Update the password column name if needed (it's password_hash in schema but we use password in code)
-- This is just to ensure consistency
ALTER TABLE users 
CHANGE COLUMN password_hash password VARCHAR(255) NOT NULL;
