# Database Setup Instructions for Roles & Permissions

## Quick Setup (Recommended)

### Option 1: Using the PHP Setup Script

1. Open your web browser
2. Navigate to: `http://your-domain.com/backend/database/setup_roles.php`
3. The script will automatically:
   - Create all necessary tables
   - Insert default roles (Super Admin, Admin, Teacher, etc.)
   - Insert all permissions
   - Assign default permissions to each role
   - Show you a summary of what was created

4. **Important:** After successful setup, delete or rename the `setup_roles.php` file for security

### Option 2: Using MySQL Command Line

If you prefer to run the SQL directly:

```bash
# Navigate to the database folder
cd backend/database

# Run the SQL script
mysql -u your_username -p your_database_name < roles_permissions.sql
```

Replace:
- `your_username` with your MySQL username
- `your_database_name` with your database name

### Option 3: Using phpMyAdmin

1. Open phpMyAdmin
2. Select your database
3. Click on the "SQL" tab
4. Open the file `backend/database/roles_permissions.sql`
5. Copy all the SQL code
6. Paste it into the SQL query box
7. Click "Go" to execute

## What Gets Created

### Tables:
1. **roles** - Stores user roles (8 default roles)
2. **permissions** - Stores all system permissions (50+ permissions)
3. **role_permissions** - Links roles to their permissions
4. **activity_logs** - Tracks all permission changes
5. **users.role_id** - Adds role_id column to existing users table

### Default Roles:
- Super Administrator (Level 100)
- Administrator (Level 80)
- Teacher (Level 50)
- Accountant (Level 60)
- Librarian (Level 40)
- Receptionist (Level 30)
- Parent (Level 10)
- Student (Level 5)

### Permission Modules:
- Dashboard (3 permissions)
- Students (8 permissions)
- Teachers (5 permissions)
- Academics (8 permissions)
- Finance (5 permissions)
- Library (4 permissions)
- Events & Gallery (7 permissions)
- Communication (4 permissions)
- Settings (7 permissions)

## After Setup

### 1. Assign Roles to Existing Users

You need to update your existing users to assign them roles:

```sql
-- Example: Make a user Super Admin
UPDATE users SET role_id = 1 WHERE email = 'admin@school.com';

-- Example: Make a user Teacher
UPDATE users SET role_id = 3 WHERE email = 'teacher@school.com';
```

Role IDs:
- 1 = Super Administrator
- 2 = Administrator
- 3 = Teacher
- 4 = Accountant
- 5 = Librarian
- 6 = Receptionist
- 7 = Parent
- 8 = Student

### 2. Access the Roles Management Page

Navigate to: `backend/admin/roles.html`

From here you can:
- View all roles
- Manage permissions for each role
- See how many users have each role
- Customize access levels

### 3. Test the System

1. Login with different user accounts
2. Verify they can only access features they have permission for
3. Try to access restricted features to ensure permissions work

## Troubleshooting

### Error: "Table already exists"
This is normal if you run the script multiple times. The script uses `CREATE TABLE IF NOT EXISTS` so it won't break existing tables.

### Error: "Cannot add foreign key constraint"
Make sure the `users` table exists before running this script.

### Error: "Access denied"
Check your database credentials in `backend/config/database.php`

### Users can't login after setup
Make sure you've assigned roles to your users using the UPDATE query above.

## Security Notes

1. **Delete setup_roles.php** after successful installation
2. Always have at least one Super Admin user
3. Never delete the Super Admin role
4. Regularly review and audit permissions
5. Monitor the activity_logs table for suspicious activity

## Need Help?

If you encounter any issues:
1. Check the error messages carefully
2. Verify your database connection settings
3. Make sure you have proper MySQL permissions
4. Check the PHP error logs
5. Ensure all files are in the correct directories

## File Structure

```
backend/
├── database/
│   ├── roles_permissions.sql      (SQL script)
│   ├── setup_roles.php            (PHP setup script)
│   └── SETUP_INSTRUCTIONS.md      (This file)
├── api/
│   ├── roles/
│   │   ├── list.php
│   │   └── assign_permissions.php
│   └── permissions/
│       └── list.php
├── middleware/
│   └── auth.php                   (Authentication & permissions)
└── admin/
    ├── roles.html                 (Management interface)
    └── js/
        └── roles.js
```
