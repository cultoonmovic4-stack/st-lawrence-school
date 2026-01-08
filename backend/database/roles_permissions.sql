-- ============================================
-- USER ROLES AND PERMISSIONS SYSTEM
-- St. Lawrence School Management System
-- ============================================

-- Create Roles Table
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    role_display_name VARCHAR(100) NOT NULL,
    description TEXT,
    level INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Permissions Table
CREATE TABLE IF NOT EXISTS permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    permission_name VARCHAR(100) UNIQUE NOT NULL,
    permission_display_name VARCHAR(150) NOT NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Role Permissions Junction Table
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    granted_by INT,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role_id, permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add role_id to users table (if not exists)
ALTER TABLE users ADD COLUMN IF NOT EXISTS role_id INT DEFAULT NULL;
ALTER TABLE users ADD FOREIGN KEY IF NOT EXISTS (role_id) REFERENCES roles(id) ON DELETE SET NULL;

-- Insert Default Roles
INSERT INTO roles (role_name, role_display_name, description, level) VALUES
('super_admin', 'Super Administrator', 'Full system access with all privileges', 100),
('admin', 'Administrator', 'School administrator with management access', 80),
('teacher', 'Teacher', 'Teaching staff with classroom management access', 50),
('accountant', 'Accountant', 'Financial management and reporting access', 60),
('librarian', 'Librarian', 'Library management access', 40),
('receptionist', 'Receptionist', 'Front desk and basic administrative access', 30),
('parent', 'Parent', 'Parent portal access to view student information', 10),
('student', 'Student', 'Student portal access', 5)
ON DUPLICATE KEY UPDATE role_display_name=VALUES(role_display_name);

-- Insert Permissions by Module
-- Dashboard Permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('dashboard.view', 'View Dashboard', 'Dashboard', 'Access to main dashboard'),
('dashboard.view_analytics', 'View Analytics', 'Dashboard', 'View detailed analytics and reports'),
('dashboard.export', 'Export Reports', 'Dashboard', 'Export dashboard reports')
ON DUPLICATE KEY UPDATE permission_display_name=VALUES(permission_display_name);

-- Student Management Permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('students.view', 'View Students', 'Students', 'View student list and details'),
('students.create', 'Add Students', 'Students', 'Register new students'),
('students.edit', 'Edit Students', 'Students', 'Modify student information'),
('students.delete', 'Delete Students', 'Students', 'Remove students from system'),
('students.view_grades', 'View Student Grades', 'Students', 'Access student academic records'),
('students.edit_grades', 'Edit Student Grades', 'Students', 'Modify student grades'),
('students.view_attendance', 'View Attendance', 'Students', 'View student attendance records'),
('students.mark_attendance', 'Mark Attendance', 'Students', 'Record student attendance')
ON DUPLICATE KEY UPDATE permission_display_name=VALUES(permission_display_name);

-- Teacher Management Permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('teachers.view', 'View Teachers', 'Teachers', 'View teacher list and details'),
('teachers.create', 'Add Teachers', 'Teachers', 'Register new teachers'),
('teachers.edit', 'Edit Teachers', 'Teachers', 'Modify teacher information'),
('teachers.delete', 'Delete Teachers', 'Teachers', 'Remove teachers from system'),
('teachers.assign_classes', 'Assign Classes', 'Teachers', 'Assign teachers to classes')
ON DUPLICATE KEY UPDATE permission_display_name=VALUES(permission_display_name);

-- Academic Management Permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('academics.view_classes', 'View Classes', 'Academics', 'View class information'),
('academics.manage_classes', 'Manage Classes', 'Academics', 'Create and modify classes'),
('academics.view_subjects', 'View Subjects', 'Academics', 'View subject information'),
('academics.manage_subjects', 'Manage Subjects', 'Academics', 'Create and modify subjects'),
('academics.view_timetable', 'View Timetable', 'Academics', 'View class schedules'),
('academics.manage_timetable', 'Manage Timetable', 'Academics', 'Create and modify timetables'),
('academics.view_exams', 'View Exams', 'Academics', 'View exam schedules and results'),
('academics.manage_exams', 'Manage Exams', 'Academics', 'Create and manage exams')
ON DUPLICATE KEY UPDATE permission_display_name=VALUES(permission_display_name);

-- Finance Permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('finance.view', 'View Finance', 'Finance', 'View financial information'),
('finance.manage_fees', 'Manage Fees', 'Finance', 'Manage fee structure and collection'),
('finance.view_reports', 'View Financial Reports', 'Finance', 'Access financial reports'),
('finance.manage_expenses', 'Manage Expenses', 'Finance', 'Record and manage expenses'),
('finance.manage_payroll', 'Manage Payroll', 'Finance', 'Process staff payroll')
ON DUPLICATE KEY UPDATE permission_display_name=VALUES(permission_display_name);

-- Library Permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('library.view', 'View Library', 'Library', 'View library resources'),
('library.manage_books', 'Manage Books', 'Library', 'Add, edit, and remove books'),
('library.issue_books', 'Issue Books', 'Library', 'Issue and return books'),
('library.view_reports', 'View Library Reports', 'Library', 'Access library reports')
ON DUPLICATE KEY UPDATE permission_display_name=VALUES(permission_display_name);

-- Events & Content Permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('events.view', 'View Events', 'Events', 'View school events'),
('events.create', 'Create Events', 'Events', 'Create new events'),
('events.edit', 'Edit Events', 'Events', 'Modify event information'),
('events.delete', 'Delete Events', 'Events', 'Remove events'),
('gallery.view', 'View Gallery', 'Gallery', 'View gallery images'),
('gallery.upload', 'Upload Images', 'Gallery', 'Upload images to gallery'),
('gallery.delete', 'Delete Images', 'Gallery', 'Remove images from gallery')
ON DUPLICATE KEY UPDATE permission_display_name=VALUES(permission_display_name);

-- Communication Permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('communication.view_messages', 'View Messages', 'Communication', 'View contact messages'),
('communication.send_sms', 'Send SMS', 'Communication', 'Send SMS notifications'),
('communication.send_email', 'Send Email', 'Communication', 'Send email notifications'),
('communication.send_notifications', 'Send Notifications', 'Communication', 'Send system notifications')
ON DUPLICATE KEY UPDATE permission_display_name=VALUES(permission_display_name);

-- System Settings Permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('settings.view', 'View Settings', 'Settings', 'View system settings'),
('settings.edit_school_info', 'Edit School Info', 'Settings', 'Modify school information'),
('settings.manage_users', 'Manage Users', 'Settings', 'Create and manage user accounts'),
('settings.manage_roles', 'Manage Roles', 'Settings', 'Create and manage user roles'),
('settings.manage_permissions', 'Manage Permissions', 'Settings', 'Assign permissions to roles'),
('settings.system_backup', 'System Backup', 'Settings', 'Perform system backups'),
('settings.view_logs', 'View System Logs', 'Settings', 'Access system activity logs')
ON DUPLICATE KEY UPDATE permission_display_name=VALUES(permission_display_name);

-- Assign Permissions to Super Admin (All Permissions)
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.role_name = 'super_admin'
ON DUPLICATE KEY UPDATE role_id=role_id;

-- Assign Permissions to Admin
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.role_name = 'admin'
AND p.permission_name NOT IN (
    'settings.manage_roles',
    'settings.manage_permissions',
    'settings.system_backup'
)
ON DUPLICATE KEY UPDATE role_id=role_id;

-- Assign Permissions to Teacher
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.role_name = 'teacher'
AND p.permission_name IN (
    'dashboard.view',
    'students.view',
    'students.view_grades',
    'students.edit_grades',
    'students.view_attendance',
    'students.mark_attendance',
    'academics.view_classes',
    'academics.view_subjects',
    'academics.view_timetable',
    'academics.view_exams',
    'library.view',
    'events.view',
    'gallery.view'
)
ON DUPLICATE KEY UPDATE role_id=role_id;

-- Assign Permissions to Accountant
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.role_name = 'accountant'
AND p.permission_name LIKE 'finance.%'
OR p.permission_name IN ('dashboard.view', 'students.view')
ON DUPLICATE KEY UPDATE role_id=role_id;

-- Assign Permissions to Librarian
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.role_name = 'librarian'
AND p.permission_name LIKE 'library.%'
OR p.permission_name IN ('dashboard.view', 'students.view')
ON DUPLICATE KEY UPDATE role_id=role_id;

-- Assign Permissions to Receptionist
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE r.role_name = 'receptionist'
AND p.permission_name IN (
    'dashboard.view',
    'students.view',
    'teachers.view',
    'communication.view_messages',
    'events.view'
)
ON DUPLICATE KEY UPDATE role_id=role_id;

-- Create Activity Log Table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_module (module),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
