-- ============================================
-- POPULATE PERMISSIONS AND ROLE PERMISSIONS
-- St. Lawrence Junior School
-- ============================================

USE st_lawrence_school;

-- Clear existing permissions (if any)
DELETE FROM role_permissions;
DELETE FROM permissions;

-- ============================================
-- INSERT PERMISSIONS BY MODULE
-- ============================================

-- User Management Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('user.view', 'user_management', 'View users list'),
('user.create', 'user_management', 'Create new users'),
('user.edit', 'user_management', 'Edit user information'),
('user.delete', 'user_management', 'Delete users'),
('user.manage_roles', 'user_management', 'Assign roles to users'),
('user.view_activity', 'user_management', 'View user activity logs');

-- Student Management Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('student.view', 'student_management', 'View students list'),
('student.create', 'student_management', 'Add new students'),
('student.edit', 'student_management', 'Edit student information'),
('student.delete', 'student_management', 'Delete students'),
('student.view_details', 'student_management', 'View detailed student information');

-- Staff Management Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('staff.view', 'staff_management', 'View staff list'),
('staff.create', 'staff_management', 'Add new staff members'),
('staff.edit', 'staff_management', 'Edit staff information'),
('staff.delete', 'staff_management', 'Delete staff members');

-- Admissions Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('admission.view', 'admissions', 'View admission applications'),
('admission.create', 'admissions', 'Create admission applications'),
('admission.edit', 'admissions', 'Edit admission applications'),
('admission.delete', 'admissions', 'Delete admission applications'),
('admission.approve', 'admissions', 'Approve/reject applications'),
('admission.review', 'admissions', 'Review and update application status');

-- Finance Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('finance.view', 'finance', 'View financial records'),
('finance.fees.manage', 'finance', 'Manage fee structure'),
('finance.payment.record', 'finance', 'Record fee payments'),
('finance.payment.view', 'finance', 'View payment records'),
('finance.expense.create', 'finance', 'Record expenses'),
('finance.expense.view', 'finance', 'View expenses'),
('finance.reports', 'finance', 'Generate financial reports');

-- Academics Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('academic.class.view', 'academics', 'View classes'),
('academic.class.manage', 'academics', 'Manage classes'),
('academic.subject.view', 'academics', 'View subjects'),
('academic.subject.manage', 'academics', 'Manage subjects'),
('academic.timetable.view', 'academics', 'View timetable'),
('academic.timetable.manage', 'academics', 'Manage timetable'),
('academic.exam.view', 'academics', 'View exams'),
('academic.exam.manage', 'academics', 'Manage exams'),
('academic.results.view', 'academics', 'View exam results'),
('academic.results.enter', 'academics', 'Enter exam results'),
('academic.attendance.view', 'academics', 'View attendance'),
('academic.attendance.mark', 'academics', 'Mark attendance');

-- Library Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('library.view', 'library', 'View library resources'),
('library.upload', 'library', 'Upload library resources'),
('library.edit', 'library', 'Edit library resources'),
('library.delete', 'library', 'Delete library resources'),
('library.download', 'library', 'Download library resources');

-- Content Management Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('content.teacher.view', 'content', 'View teachers'),
('content.teacher.manage', 'content', 'Manage teachers'),
('content.gallery.view', 'content', 'View gallery'),
('content.gallery.manage', 'content', 'Manage gallery'),
('content.event.view', 'content', 'View events'),
('content.event.manage', 'content', 'Manage events'),
('content.testimonial.view', 'content', 'View testimonials'),
('content.testimonial.manage', 'content', 'Manage testimonials'),
('content.calendar.view', 'content', 'View academic calendar'),
('content.calendar.manage', 'content', 'Manage academic calendar');

-- Communications Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('comm.message.view', 'communications', 'View messages'),
('comm.message.send', 'communications', 'Send messages'),
('comm.message.reply', 'communications', 'Reply to messages'),
('comm.notification.send', 'communications', 'Send notifications'),
('comm.sms.send', 'communications', 'Send SMS'),
('comm.email.send', 'communications', 'Send emails');

-- Reports Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('report.student', 'reports', 'Generate student reports'),
('report.financial', 'reports', 'Generate financial reports'),
('report.academic', 'reports', 'Generate academic reports'),
('report.attendance', 'reports', 'Generate attendance reports'),
('report.custom', 'reports', 'Create custom reports');

-- Settings Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('settings.view', 'settings', 'View system settings'),
('settings.edit', 'settings', 'Edit system settings'),
('settings.backup', 'settings', 'Backup system data'),
('settings.restore', 'settings', 'Restore system data');

-- Dashboard Permissions
INSERT INTO permissions (permission_name, permission_module, description) VALUES
('dashboard.view', 'dashboard', 'View dashboard'),
('dashboard.stats', 'dashboard', 'View statistics');

-- ============================================
-- ASSIGN PERMISSIONS TO ROLES
-- ============================================

-- 1. SUPER ADMINISTRATOR (ID: 1) - ALL PERMISSIONS
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions;

-- 2. ADMINISTRATOR (ID: 2) - Most permissions except system settings
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, id FROM permissions 
WHERE permission_name NOT IN ('settings.backup', 'settings.restore', 'user.delete');

-- 3. TEACHER (ID: 3) - Academic and classroom focused
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, id FROM permissions 
WHERE permission_module IN ('academics', 'library', 'communications', 'reports', 'dashboard')
AND permission_name IN (
    'academic.class.view', 'academic.subject.view', 'academic.timetable.view',
    'academic.exam.view', 'academic.results.view', 'academic.results.enter',
    'academic.attendance.view', 'academic.attendance.mark',
    'library.view', 'library.upload', 'library.edit',
    'comm.message.view', 'comm.message.send', 'comm.message.reply',
    'report.student', 'report.academic', 'report.attendance',
    'dashboard.view', 'dashboard.stats',
    'student.view', 'student.view_details'
);

-- 4. ACCOUNTANT (ID: 4) - Finance focused
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, id FROM permissions 
WHERE permission_module IN ('finance', 'reports', 'dashboard', 'student_management')
AND permission_name IN (
    'finance.view', 'finance.fees.manage', 'finance.payment.record', 
    'finance.payment.view', 'finance.expense.create', 'finance.expense.view', 
    'finance.reports',
    'report.financial', 'report.student',
    'dashboard.view', 'dashboard.stats',
    'student.view', 'student.view_details',
    'comm.message.send'
);

-- 5. LIBRARIAN (ID: 5) - Library focused
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, id FROM permissions 
WHERE permission_module IN ('library', 'dashboard')
AND permission_name IN (
    'library.view', 'library.upload', 'library.edit', 'library.delete', 'library.download',
    'dashboard.view', 'dashboard.stats'
);

-- 6. RECEPTIONIST (ID: 6) - Front desk operations
INSERT INTO role_permissions (role_id, permission_id)
SELECT 6, id FROM permissions 
WHERE permission_name IN (
    'admission.view', 'admission.create', 'admission.edit',
    'comm.message.view', 'comm.message.reply',
    'student.view', 'student.view_details',
    'content.event.view', 'content.calendar.view',
    'dashboard.view', 'dashboard.stats'
);

-- 7. PARENT (ID: 7) - Parent portal (read-only mostly)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 7, id FROM permissions 
WHERE permission_name IN (
    'student.view_details',
    'academic.results.view', 'academic.attendance.view', 'academic.timetable.view',
    'finance.payment.view',
    'library.view', 'library.download',
    'content.event.view', 'content.calendar.view',
    'comm.message.send', 'comm.message.view'
);

-- 8. STUDENT (ID: 8) - Student portal (read-only)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 8, id FROM permissions 
WHERE permission_name IN (
    'academic.results.view', 'academic.attendance.view', 'academic.timetable.view',
    'library.view', 'library.download',
    'content.event.view', 'content.calendar.view'
);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Count permissions per role
SELECT 
    r.role_name,
    COUNT(rp.permission_id) as permission_count
FROM roles r
LEFT JOIN role_permissions rp ON r.id = rp.role_id
GROUP BY r.id, r.role_name
ORDER BY r.role_level DESC;

-- View all permissions by module
SELECT 
    permission_module,
    COUNT(*) as permission_count
FROM permissions
GROUP BY permission_module
ORDER BY permission_module;

-- ============================================
-- COMPLETED
-- ============================================
