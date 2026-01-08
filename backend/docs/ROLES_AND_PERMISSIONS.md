# User Roles and Permissions System

## Overview
The St. Lawrence School Management System includes a comprehensive role-based access control (RBAC) system that allows fine-grained control over user permissions.

## Default Roles

### 1. Super Administrator (Level 100)
- **Full system access** with all privileges
- Can manage all aspects of the system
- Cannot be deleted or modified
- Automatically has all permissions

### 2. Administrator (Level 80)
- School administrator with management access
- Can manage students, teachers, academics, and content
- Cannot manage system roles or perform system backups
- Suitable for school principals and head administrators

### 3. Teacher (Level 50)
- Teaching staff with classroom management access
- Can view students and mark attendance
- Can view and edit grades for their classes
- Can view academic schedules and exams
- Limited access to other modules

### 4. Accountant (Level 60)
- Financial management and reporting access
- Full access to finance module
- Can view student information for fee management
- Cannot access academic or teaching features

### 5. Librarian (Level 40)
- Library management access
- Can manage books and library resources
- Can issue and return books
- Can view student information for book issuance

### 6. Receptionist (Level 30)
- Front desk and basic administrative access
- Can view students and teachers
- Can view messages and events
- Limited editing capabilities

### 7. Parent (Level 10)
- Parent portal access
- Can view their children's information
- Can view grades and attendance
- Cannot modify any data

### 8. Student (Level 5)
- Student portal access
- Can view their own information
- Can view assignments and grades
- Cannot modify any data

## Permission Modules

### Dashboard
- `dashboard.view` - Access to main dashboard
- `dashboard.view_analytics` - View detailed analytics
- `dashboard.export` - Export dashboard reports

### Students
- `students.view` - View student list and details
- `students.create` - Register new students
- `students.edit` - Modify student information
- `students.delete` - Remove students
- `students.view_grades` - Access academic records
- `students.edit_grades` - Modify grades
- `students.view_attendance` - View attendance records
- `students.mark_attendance` - Record attendance

### Teachers
- `teachers.view` - View teacher list
- `teachers.create` - Register new teachers
- `teachers.edit` - Modify teacher information
- `teachers.delete` - Remove teachers
- `teachers.assign_classes` - Assign teachers to classes

### Academics
- `academics.view_classes` - View class information
- `academics.manage_classes` - Create/modify classes
- `academics.view_subjects` - View subjects
- `academics.manage_subjects` - Create/modify subjects
- `academics.view_timetable` - View schedules
- `academics.manage_timetable` - Create/modify timetables
- `academics.view_exams` - View exam schedules
- `academics.manage_exams` - Create/manage exams

### Finance
- `finance.view` - View financial information
- `finance.manage_fees` - Manage fee structure
- `finance.view_reports` - Access financial reports
- `finance.manage_expenses` - Record expenses
- `finance.manage_payroll` - Process payroll

### Library
- `library.view` - View library resources
- `library.manage_books` - Add/edit/remove books
- `library.issue_books` - Issue and return books
- `library.view_reports` - Access library reports

### Events & Content
- `events.view` - View school events
- `events.create` - Create new events
- `events.edit` - Modify events
- `events.delete` - Remove events
- `gallery.view` - View gallery images
- `gallery.upload` - Upload images
- `gallery.delete` - Remove images

### Communication
- `communication.view_messages` - View contact messages
- `communication.send_sms` - Send SMS notifications
- `communication.send_email` - Send email notifications
- `communication.send_notifications` - Send system notifications

### Settings
- `settings.view` - View system settings
- `settings.edit_school_info` - Modify school information
- `settings.manage_users` - Create/manage user accounts
- `settings.manage_roles` - Create/manage user roles
- `settings.manage_permissions` - Assign permissions to roles
- `settings.system_backup` - Perform system backups
- `settings.view_logs` - Access system activity logs

## Database Setup

1. Run the SQL script to create tables:
```bash
mysql -u username -p database_name < backend/database/roles_permissions.sql
```

2. The script will:
   - Create `roles` table
   - Create `permissions` table
   - Create `role_permissions` junction table
   - Add `role_id` column to `users` table
   - Insert default roles
   - Insert all permissions
   - Assign default permissions to each role
   - Create `activity_logs` table

## Usage

### Assigning Roles to Users

When creating or editing a user, assign them a role:

```php
$role_id = 3; // Teacher role
$query = "UPDATE users SET role_id = :role_id WHERE id = :user_id";
```

### Checking Permissions in PHP

Use the middleware functions:

```php
require_once '../middleware/auth.php';

// Check if user is authenticated
$auth = authenticate();
if (!$auth['success']) {
    // Handle unauthorized access
}

// Check specific permission
if (!hasPermission($auth['user']['id'], 'students.create')) {
    // User doesn't have permission
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit;
}
```

### Managing Permissions via UI

1. Navigate to **Settings > User Roles**
2. Click **Manage Permissions** on any role card
3. Select/deselect permissions by module
4. Click **Save Permissions**

### Activity Logging

All permission changes are automatically logged:

```php
logActivity($db, $user_id, 'assign_permissions', 'Roles', 'Description');
```

## Security Best Practices

1. **Never delete the Super Admin role**
2. **Always have at least one Super Admin user**
3. **Review permissions regularly**
4. **Use principle of least privilege** - give users only the permissions they need
5. **Monitor activity logs** for suspicious behavior
6. **Backup the database** before making major permission changes

## API Endpoints

- `GET /api/roles/list.php` - List all roles
- `GET /api/permissions/list.php` - List all permissions
- `GET /api/permissions/list.php?role_id=X` - List permissions for specific role
- `POST /api/roles/assign_permissions.php` - Assign permissions to role

## Future Enhancements

- Custom role creation
- Permission inheritance
- Time-based permissions
- IP-based access control
- Two-factor authentication for sensitive operations
- Detailed audit trails
