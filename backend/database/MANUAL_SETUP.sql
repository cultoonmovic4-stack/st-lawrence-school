-- ============================================
-- MANUAL SETUP - ROLES & PERMISSIONS
-- Copy and paste each section one at a time
-- ============================================

-- STEP 1: Create roles table
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    role_display_name VARCHAR(100) NOT NULL,
    description TEXT,
    level INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- STEP 2: Create permissions table
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    permission_name VARCHAR(100) UNIQUE NOT NULL,
    permission_display_name VARCHAR(150) NOT NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- STEP 3: Create role_permissions junction table
CREATE TABLE role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    granted_by INT,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role_id, permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- STEP 4: Create activity_logs table
CREATE TABLE activity_logs (
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

-- STEP 5: Add role_id column to users table
ALTER TABLE users ADD COLUMN role_id INT DEFAULT NULL;

-- STEP 6: Add foreign key to users table
ALTER TABLE users ADD FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL;

-- STEP 7: Insert default roles
INSERT INTO roles (role_name, role_display_name, description, level) VALUES
('super_admin', 'Super Administrator', 'Full system access with all privileges', 100),
('admin', 'Administrator', 'School administrator with management access', 80),
('teacher', 'Teacher', 'Teaching staff with classroom management access', 50),
('accountant', 'Accountant', 'Financial management and reporting access', 60),
('librarian', 'Librarian', 'Library management access', 40),
('receptionist', 'Receptionist', 'Front desk and basic administrative access', 30),
('parent', 'Parent', 'Parent portal access to view student information', 10),
('student', 'Student', 'Student portal access', 5);

-- STEP 8: Insert Dashboard permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('dashboard.view', 'View Dashboard', 'Dashboard', 'Access to main dashboard'),
('dashboard.view_analytics', 'View Analytics', 'Dashboard', 'View detailed analytics and reports'),
('dashboard.export', 'Export Reports', 'Dashboard', 'Export dashboard reports');

-- STEP 9: Insert Student permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('students.view', 'View Students', 'Students', 'View student list and details'),
('students.create', 'Add Students', 'Students', 'Register new students'),
('students.edit', 'Edit Students', 'Students', 'Modify student information'),
('students.delete', 'Delete Students', 'Students', 'Remove students from system'),
('students.view_grades', 'View Student Grades', 'Students', 'Access student academic records'),
('students.edit_grades', 'Edit Student Grades', 'Students', 'Modify student grades'),
('students.view_attendance', 'View Attendance', 'Students', 'View student attendance records'),
('students.mark_attendance', 'Mark Attendance', 'Students', 'Record student attendance');

-- STEP 10: Insert Teacher permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('teachers.view', 'View Teachers', 'Teachers', 'View teacher list and details'),
('teachers.create', 'Add Teachers', 'Teachers', 'Register new teachers'),
('teachers.edit', 'Edit Teachers', 'Teachers', 'Modify teacher information'),
('teachers.delete', 'Delete Teachers', 'Teachers', 'Remove teachers from system'),
('teachers.assign_classes', 'Assign Classes', 'Teachers', 'Assign teachers to classes');

-- STEP 11: Insert Academic permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('academics.view_classes', 'View Classes', 'Academics', 'View class information'),
('academics.manage_classes', 'Manage Classes', 'Academics', 'Create and modify classes'),
('academics.view_subjects', 'View Subjects', 'Academics', 'View subject information'),
('academics.manage_subjects', 'Manage Subjects', 'Academics', 'Create and modify subjects'),
('academics.view_timetable', 'View Timetable', 'Academics', 'View class schedules'),
('academics.manage_timetable', 'Manage Timetable', 'Academics', 'Create and modify timetables'),
('academics.view_exams', 'View Exams', 'Academics', 'View exam schedules and results'),
('academics.manage_exams', 'Manage Exams', 'Academics', 'Create and manage exams');

-- STEP 12: Insert Finance permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('finance.view', 'View Finance', 'Finance', 'View financial information'),
('finance.manage_fees', 'Manage Fees', 'Finance', 'Manage fee structure and collection'),
('finance.view_reports', 'View Financial Reports', 'Finance', 'Access financial reports'),
('finance.manage_expenses', 'Manage Expenses', 'Finance', 'Record and manage expenses'),
('finance.manage_payroll', 'Manage Payroll', 'Finance', 'Process staff payroll');

-- STEP 13: Insert Library permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('library.view', 'View Library', 'Library', 'View library resources'),
('library.manage_books', 'Manage Books', 'Library', 'Add, edit, and remove books'),
('library.issue_books', 'Issue Books', 'Library', 'Issue and return books'),
('library.view_reports', 'View Library Reports', 'Library', 'Access library reports');

-- STEP 14: Insert Events & Gallery permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('events.view', 'View Events', 'Events', 'View school events'),
('events.create', 'Create Events', 'Events', 'Create new events'),
('events.edit', 'Edit Events', 'Events', 'Modify event information'),
('events.delete', 'Delete Events', 'Events', 'Remove events'),
('gallery.view', 'View Gallery', 'Gallery', 'View gallery images'),
('gallery.upload', 'Upload Images', 'Gallery', 'Upload images to gallery'),
('gallery.delete', 'Delete Images', 'Gallery', 'Remove images from gallery');

-- STEP 15: Insert Communication permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('communication.view_messages', 'View Messages', 'Communication', 'View contact messages'),
('communication.send_sms', 'Send SMS', 'Communication', 'Send SMS notifications'),
('communication.send_email', 'Send Email', 'Communication', 'Send email notifications'),
('communication.send_notifications', 'Send Notifications', 'Communication', 'Send system notifications');

-- STEP 16: Insert Settings permissions
INSERT INTO permissions (permission_name, permission_display_name, module, description) VALUES
('settings.view', 'View Settings', 'Settings', 'View system settings'),
('settings.edit_school_info', 'Edit School Info', 'Settings', 'Modify school information'),
('settings.manage_users', 'Manage Users', 'Settings', 'Create and manage user accounts'),
('settings.manage_roles', 'Manage Roles', 'Settings', 'Create and manage user roles'),
('settings.manage_permissions', 'Manage Permissions', 'Settings', 'Assign permissions to roles'),
('settings.system_backup', 'System Backup', 'Settings', 'Perform system backups'),
('settings.view_logs', 'View System Logs', 'Settings', 'Access system activity logs');

-- STEP 17: Assign ALL permissions to Super Admin
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

-- STEP 18: Assign permissions to Admin (all except role management and backup)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions 
WHERE permission_name NOT IN ('settings.manage_roles', 'settings.manage_permissions', 'settings.system_backup');

-- STEP 19: Assign permissions to Teacher
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions 
WHERE permission_name IN (
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
);

-- STEP 20: Assign permissions to Accountant
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, id FROM permissions 
WHERE permission_name LIKE 'finance.%' OR permission_name IN ('dashboard.view', 'students.view');

-- STEP 21: Assign permissions to Librarian
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, id FROM permissions 
WHERE permission_name LIKE 'library.%' OR permission_name IN ('dashboard.view', 'students.view');

-- STEP 22: Assign permissions to Receptionist
INSERT INTO role_permissions (role_id, permission_id)
SELECT 6, id FROM permissions 
WHERE permission_name IN ('dashboard.view', 'students.view', 'teachers.view', 'communication.view_messages', 'events.view');

-- STEP 23: Make yourself Super Admin (CHANGE THE EMAIL!)
UPDATE users SET role_id = 1 WHERE email = 'your-email@example.com';

-- DONE! Now check your setup:
SELECT 'Roles Created:' as Status, COUNT(*) as Count FROM roles
UNION ALL
SELECT 'Permissions Created:', COUNT(*) FROM permissions
UNION ALL
SELECT 'Assignments Created:', COUNT(*) FROM role_permissions;
