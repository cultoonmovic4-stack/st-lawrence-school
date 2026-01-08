# St. Lawrence Junior School - School Management System

A comprehensive web-based school management system for St. Lawrence Junior School Kabowa, featuring a modern admin dashboard, student management, teacher management, events, library, gallery, and more.

## 🎓 Project Overview

This is a full-stack school management system built with PHP, MySQL, HTML, CSS, and JavaScript. It provides a complete solution for managing all aspects of a school's operations including students, teachers, events, library resources, admissions, and communications.

## ✨ Features

### 🏠 Public Website
- **Home Page** - Modern landing page with school information
- **About Us** - School history, mission, and vision
- **Admissions** - Online admission application system
- **Events** - School events calendar and announcements
- **Gallery** - Photo gallery of school activities
- **Library** - Digital library with downloadable resources
- **Teachers** - Staff directory with profiles
- **Contact** - Contact form and school information

### 🔐 Admin Dashboard
- **Modern UI** - Clean, professional dark navy theme
- **Dashboard Overview** - Real-time statistics and analytics
- **Quick Actions** - Fast access to common tasks
- **Recent Activity** - Timeline of recent system activities
- **Data Visualization** - Interactive pie charts and graphs
- **Inspirational Quotes** - Word of the Day section

### 👥 User Management
- **Role-Based Access Control (RBAC)** - 8 predefined roles
- **Permissions System** - Granular permission management
- **User Roles:**
  - Super Administrator (Level 100)
  - Administrator (Level 80)
  - Teacher (Level 50)
  - Accountant (Level 60)
  - Librarian (Level 40)
  - Receptionist (Level 30)
  - Parent (Level 10)
  - Student (Level 5)

### 📊 Management Modules
- **Students Management** - Registration, profiles, grades, attendance
- **Teachers Management** - Staff profiles, assignments, schedules
- **Academic Management** - Classes, subjects, timetables, exams
- **Finance Management** - Fee collection, expenses, payroll
- **Library Management** - Books, resources, issue/return tracking
- **Events Management** - Create and manage school events
- **Gallery Management** - Upload and organize photos
- **Communications** - Messages, SMS, email, notifications

### 🔒 Security Features
- **Authentication System** - Secure login with password hashing
- **Session Management** - Secure session handling
- **Permission Checks** - Middleware for access control
- **Activity Logging** - Track all user actions
- **SQL Injection Protection** - Prepared statements
- **XSS Protection** - Input sanitization

## 🛠️ Technology Stack

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with gradients and animations
- **JavaScript (ES6+)** - Dynamic functionality
- **Font Awesome 6** - Icon library
- **Chart.js** - Data visualization

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL 5.7+** - Database management
- **PDO** - Database abstraction layer

### Server
- **Apache/XAMPP** - Local development server
- **mod_rewrite** - URL rewriting

## 📁 Project Structure

```
st-lawrence-school/
├── backend/
│   ├── admin/
│   │   ├── css/
│   │   │   └── admin-style.css
│   │   ├── js/
│   │   │   ├── auth.js
│   │   │   ├── dashboard.js
│   │   │   ├── roles.js
│   │   │   └── events.js
│   │   ├── dashboard.html
│   │   ├── index.html (login)
│   │   ├── roles.html
│   │   ├── teachers.html
│   │   ├── events.html
│   │   ├── library.html
│   │   └── gallery.html
│   ├── api/
│   │   ├── config/
│   │   │   └── database.php
│   │   ├── auth/
│   │   ├── teachers/
│   │   ├── events/
│   │   ├── library/
│   │   ├── gallery/
│   │   ├── roles/
│   │   └── permissions/
│   ├── middleware/
│   │   └── auth.php
│   ├── database/
│   │   ├── CREATE_ALL_TABLES.sql
│   │   ├── roles_permissions.sql
│   │   └── MANUAL_SETUP.sql
│   ├── docs/
│   │   └── ROLES_AND_PERMISSIONS.md
│   └── uploads/
├── css/
├── js/
├── img/
├── index.html
├── about.html
├── admissions.html
├── events.html
├── gallery.html
├── library.html
├── teachers.html
├── contact.html
└── README.md
```

## 🚀 Installation

### Prerequisites
- XAMPP/WAMP/LAMP (Apache, MySQL, PHP)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Step 1: Clone/Download Project
```bash
# Place the project in your web server directory
# For XAMPP: C:\xampp\htdocs\st-lawrence-school
```

### Step 2: Database Setup
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database: `st_lawrence_school`
3. Import the main database:
   - Go to SQL tab
   - Open `backend/database/CREATE_ALL_TABLES.sql`
   - Execute the script

4. Import roles and permissions:
   - Go to SQL tab
   - Open `backend/database/MANUAL_SETUP.sql`
   - Execute the script

### Step 3: Configure Database Connection
Edit `backend/api/config/database.php`:
```php
private $host = "localhost";
private $db_name = "st_lawrence_school";
private $username = "root";
private $password = ""; // Your MySQL password
```

### Step 4: Set Up Admin User
Run this SQL query to create your admin account:
```sql
UPDATE users 
SET email = 'your-email@example.com', 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role_id = 1 
WHERE username = 'admin';
```
Default password: `password` (Change this immediately!)

### Step 5: Access the System
- **Public Website:** `http://localhost/st-lawrence-school/`
- **Admin Login:** `http://localhost/st-lawrence-school/backend/admin/`

## 👤 Default Login Credentials

**Super Administrator:**
- Email: `admin@stlawrence.com`
- Password: `password`

⚠️ **Important:** Change the default password immediately after first login!

## 🎨 Design Features

### Color Scheme
- **Primary:** Dark Navy Blue (#0f1419)
- **Secondary:** Blue (#1e4d9f)
- **Accent:** Red (#dc3545)
- **Success:** Green (#10b981)
- **Warning:** Orange (#ff8c00)

### UI Components
- Modern gradient backgrounds
- Smooth animations and transitions
- Responsive design for all devices
- Interactive charts and graphs
- Card-based layouts
- Dropdown menus with tree structure
- Toast notifications
- Modal dialogs

## 📱 Responsive Design

The system is fully responsive and works on:
- Desktop (1920px+)
- Laptop (1366px - 1920px)
- Tablet (768px - 1366px)
- Mobile (320px - 768px)

## 🔐 User Roles & Permissions

### Permission Modules
- Dashboard (3 permissions)
- Students (8 permissions)
- Teachers (5 permissions)
- Academics (8 permissions)
- Finance (5 permissions)
- Library (4 permissions)
- Events & Gallery (7 permissions)
- Communication (4 permissions)
- Settings (7 permissions)

**Total:** 51+ granular permissions

For detailed information, see `backend/docs/ROLES_AND_PERMISSIONS.md`

## 📊 Database Schema

### Core Tables (9)
- users
- teachers
- gallery_images
- library_resources
- events
- testimonials
- contact_submissions
- admission_applications
- calendar_events

### Enhancement Tables (20)
- admin_activity_logs
- file_uploads
- email_logs
- notifications
- settings
- departments
- subjects
- And more...

### Roles & Permissions (4)
- roles
- permissions
- role_permissions
- activity_logs

**Total:** 33 tables

## 🔧 Configuration

### Email Settings
Configure SMTP in `backend/api/config/email.php` (if exists)

### File Upload Settings
- Max file size: 10MB
- Allowed types: PDF, DOC, DOCX, JPG, PNG, GIF
- Upload directory: `backend/uploads/`

### Session Settings
- Session timeout: 30 minutes
- Secure cookies enabled
- HttpOnly flag enabled

## 🐛 Troubleshooting

### Common Issues

**1. Database Connection Error**
- Check database credentials in `database.php`
- Ensure MySQL service is running
- Verify database name exists

**2. Login Not Working**
- Clear browser cache
- Check if user exists in database
- Verify password hash

**3. Permissions Not Working**
- Ensure roles_permissions table is populated
- Check user has role_id assigned
- Verify middleware is included

**4. File Upload Fails**
- Check folder permissions (uploads folder)
- Verify file size limits in php.ini
- Check allowed file types

## 📝 API Endpoints

### Authentication
- `POST /api/auth/login.php` - User login
- `POST /api/auth/logout.php` - User logout

### Teachers
- `GET /api/teachers/list.php` - Get all teachers
- `POST /api/teachers/create.php` - Create teacher
- `PUT /api/teachers/update.php` - Update teacher
- `DELETE /api/teachers/delete.php` - Delete teacher

### Events
- `GET /api/events/list.php` - Get all events
- `POST /api/events/create.php` - Create event
- `PUT /api/events/update.php` - Update event
- `DELETE /api/events/delete.php` - Delete event

### Roles & Permissions
- `GET /api/roles/list.php` - Get all roles
- `GET /api/permissions/list.php` - Get all permissions
- `POST /api/roles/assign_permissions.php` - Assign permissions

## 🤝 Contributing

This is a school project. For improvements or bug fixes:
1. Document the issue
2. Make changes
3. Test thoroughly
4. Update documentation

## 📄 License

This project is for educational purposes for St. Lawrence Junior School Kabowa.

## 👨‍💻 Developer

Developed for St. Lawrence Junior School Kabowa
- **School:** St. Lawrence Junior School
- **Location:** Kabowa, Uganda
- **Year:** 2026

## 📞 Support

For technical support or questions:
- Email: admin@stlawrence.com
- Phone: [School Contact Number]

## 🎯 Future Enhancements

- [ ] Mobile app (iOS/Android)
- [ ] Parent portal
- [ ] Student portal
- [ ] Online payment integration
- [ ] SMS gateway integration
- [ ] Email automation
- [ ] Report card generation
- [ ] Attendance tracking with biometrics
- [ ] Online classes integration
- [ ] Assignment submission system

## 📚 Documentation

- [Roles & Permissions Guide](backend/docs/ROLES_AND_PERMISSIONS.md)
- [Admin Dashboard Guide](backend/admin/ADMIN_DASHBOARD_GUIDE.md)
- [Database Setup Instructions](backend/database/SETUP_INSTRUCTIONS.md)

## ⚡ Performance

- Optimized database queries
- Lazy loading for images
- Minified CSS/JS (production)
- Caching enabled
- CDN for libraries

## 🔄 Version History

### Version 1.0.0 (January 2026)
- Initial release
- Complete admin dashboard
- User roles and permissions system
- All core modules implemented
- Responsive design
- Security features

---

**Made with ❤️ for St. Lawrence Junior School Kabowa**
#   t e s t  
 #   n e w - t e s t  
 #   t e s t  
 #   t e s t  
 #   t e s t  
 #   n e w - t e s t  
 