# St. Lawrence Junior School - Kabowa
## Complete School Management System with AI Assistant

---

## 📋 Table of Contents
1. [Project Overview](#project-overview)
2. [Features](#features)
3. [Technology Stack](#technology-stack)
4. [Project Structure](#project-structure)
5. [Database Schema](#database-schema)
6. [Installation Guide](#installation-guide)
7. [Backend Architecture](#backend-architecture)
8. [Frontend Architecture](#frontend-architecture)
9. [AI Chatbot System](#ai-chatbot-system)
10. [Admin Panel](#admin-panel)
11. [Authentication & Security](#authentication--security)
12. [API Documentation](#api-documentation)
13. [Deployment](#deployment)
14. [Troubleshooting](#troubleshooting)

---

## 🎯 Project Overview

St. Lawrence Junior School - Kabowa is a comprehensive school management system built for a premier mixed day and boarding primary school in Kampala, Uganda. The system includes a modern website, admin panel, and an AI-powered chatbot assistant with voice capabilities.

**School Details:**
- **Name:** St. Lawrence Junior School - Kabowa
- **Location:** Kabowa, Church Zone, Rubaga Division - Kampala District, Uganda
- **Established:** 2010
- **Type:** Mixed Day & Boarding Primary School
- **Programs:** Nursery (Baby - Top Class) & Primary (P1 - P7)
- **Contact:** +256 701 420 506 / +256 772 420 506
- **Email:** stlawrencejuniorschoolkabowa@gmail.com

---

## ✨ Features

### Public Website
- Modern, responsive design with smooth animations
- Hero section with background video
- Programs showcase (Nursery, Primary, Boarding)
- Dynamic events bar
- Teacher profiles with search and filter
- Photo gallery with categories
- Online admission application
- Fee structure display
- Library with PDF resources
- Contact information and forms

### AI Chatbot Assistant
- **Text Chat:** Real-time messaging with intelligent responses
- **Voice Chat:** Full voice-to-voice conversation using Web Speech API
- **Knowledge Base:** 35+ categories covering:
  - School information and history
  - Fees (Day Scholar & Boarding)
  - Admission process
  - Contact information and location
  - School hours and schedule
  - Programs and curriculum
  - Extracurricular activities
  - Facilities and infrastructure
  - Teachers and staff
  - Library resources
- **Features:**
  - Quick action buttons
  - Real-time transcript
  - Call timer
  - Mute functionality
  - Audio wave visualization
  - Mobile responsive design

### Admin Panel
- Secure authentication with session management
- Dashboard with statistics
- Content management (Events, Gallery, Teachers, Library)
- User management
- Admission applications review
- File upload and management
- Activity logging

---

## 🛠 Technology Stack

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with animations
- **JavaScript (ES6+)** - Interactive functionality
- **Bootstrap 5** - Responsive grid system
- **Font Awesome 6** - Icons
- **AOS Library** - Scroll animations
- **Web Speech API** - Voice recognition and synthesis

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL 5.7+** - Database management
- **PHPMailer** - Email functionality
- **Composer** - Dependency management

### Development Tools
- **XAMPP** - Local development environment
- **Git** - Version control
- **VS Code** - Code editor

---

## 📁 Project Structure

```
st-lawrence-school/
│
├── frontend/                      # Public website pages
│   ├── index-redesign.html       # Homepage
│   ├── About-redesign.html       # About page
│   ├── Teachers-redesign.html    # Teachers page
│   ├── Gallery-redesign.html     # Gallery page
│   ├── Admission-redesign.html   # Admission page
│   ├── Contact-redesign.html     # Contact page
│   ├── Library-redesign.html     # Library page
│   ├── Fees.html                 # Fee structure
│   └── School-Anthem.html        # School anthem
│
├── backend/                       # Backend system
│   ├── admin/                    # Admin panel
│   │   ├── login.html           # Admin login
│   │   ├── dashboard.html       # Admin dashboard
│   │   ├── events.html          # Events management
│   │   ├── gallery.html         # Gallery management
│   │   ├── teachers.html        # Teachers management
│   │   ├── library.html         # Library management
│   │   └── admissions.html      # Admissions management
│   │
│   ├── api/                      # API endpoints
│   │   ├── auth/                # Authentication
│   │   │   ├── login.php
│   │   │   ├── logout.php
│   │   │   └── session.php
│   │   │
│   │   ├── chatbot/             # AI Chatbot
│   │   │   ├── chat.php         # Chat API endpoint
│   │   │   └── knowledge_base.php # Knowledge base
│   │   │
│   │   ├── events/              # Events CRUD
│   │   ├── gallery/             # Gallery CRUD
│   │   ├── teachers/            # Teachers CRUD
│   │   ├── library/             # Library CRUD
│   │   ├── admissions/          # Admissions CRUD
│   │   └── setup/               # Database setup
│   │
│   ├── uploads/                  # Uploaded files
│   │   ├── events/
│   │   ├── gallery/
│   │   ├── teachers/
│   │   └── library/
│   │
│   └── vendor/                   # Composer dependencies
│
├── css/                          # Stylesheets
│   ├── redesign-style.css       # Main website styles
│   ├── chatbot.css              # Chatbot styles
│   ├── fees-styles.css          # Fee page styles
│   └── bootstrap.min.css        # Bootstrap framework
│
├── js/                           # JavaScript files
│   ├── chatbot.js               # Chatbot functionality
│   └── main.js                  # General scripts
│
├── img/                          # Images and media
│   ├── 5.jpg                    # School logo
│   └── ...                      # Other images
│
├── library_pdfs/                 # PDF resources
│
├── database_schema.sql           # Database structure
│
└── README.md                     # This file
```

---

## 🗄 Database Schema

### Tables Overview

#### 1. `users` - Admin Users
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- username (VARCHAR 50, UNIQUE)
- email (VARCHAR 100, UNIQUE)
- password (VARCHAR 255) - Hashed with password_hash()
- full_name (VARCHAR 100)
- role (ENUM: 'admin', 'editor', 'viewer')
- created_at (TIMESTAMP)
- last_login (TIMESTAMP)
```

#### 2. `events` - School Events
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- title (VARCHAR 200)
- description (TEXT)
- event_date (DATE)
- image_url (VARCHAR 255)
- created_by (INT, FOREIGN KEY -> users.id)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 3. `gallery` - Photo Gallery
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- title (VARCHAR 200)
- description (TEXT)
- image_url (VARCHAR 255)
- category (VARCHAR 50)
- uploaded_by (INT, FOREIGN KEY -> users.id)
- created_at (TIMESTAMP)
```

#### 4. `teachers` - Teacher Profiles
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- name (VARCHAR 100)
- position (VARCHAR 100)
- subject (VARCHAR 100)
- bio (TEXT)
- image_url (VARCHAR 255)
- email (VARCHAR 100)
- phone (VARCHAR 20)
- created_at (TIMESTAMP)
```

#### 5. `library` - Library Resources
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- title (VARCHAR 200)
- author (VARCHAR 100)
- category (VARCHAR 50)
- description (TEXT)
- file_url (VARCHAR 255)
- file_type (VARCHAR 10)
- uploaded_by (INT, FOREIGN KEY -> users.id)
- created_at (TIMESTAMP)
```

#### 6. `admissions` - Admission Applications
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- student_name (VARCHAR 100)
- date_of_birth (DATE)
- gender (ENUM: 'Male', 'Female')
- parent_name (VARCHAR 100)
- parent_email (VARCHAR 100)
- parent_phone (VARCHAR 20)
- address (TEXT)
- class_applying (VARCHAR 50)
- admission_type (ENUM: 'Day Scholar', 'Boarding')
- status (ENUM: 'pending', 'approved', 'rejected')
- submitted_at (TIMESTAMP)
```

---

## 🚀 Installation Guide

### Prerequisites
- XAMPP (Apache + MySQL + PHP 7.4+)
- Web browser (Chrome, Firefox, Edge)
- Text editor (VS Code recommended)

### Step 1: Clone/Download Project
```bash
# Place project in XAMPP htdocs folder
C:\xampp\htdocs\st-lawrence-school\
```

### Step 2: Environment Configuration
1. Copy `.env.example` to `.env`:
```bash
copy .env.example .env
```

2. Edit `.env` file and add your credentials:
```env
# Database Configuration
DB_HOST=localhost
DB_NAME=st_lawrence_school
DB_USER=root
DB_PASS=your_database_password

# SMTP Email Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_gmail_app_password
SMTP_FROM_EMAIL=your_email@gmail.com
SMTP_FROM_NAME=St. Lawrence Junior School
```

**Important:** Never commit the `.env` file to Git! It's already in `.gitignore`.

### Step 3: Database Setup
1. Start XAMPP (Apache + MySQL)
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Create database: `st_lawrence_school`
4. Import schema: `database_schema.sql`
5. Update database credentials in `backend/api/config/database.php`:
```php
$host = 'localhost';
$dbname = 'st_lawrence_school';
$username = 'root';
$password = '';
```

### Step 3: Create Default Admin User
```sql
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@stlawrence.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');
-- Default password: password
```

### Step 4: Configure File Permissions
Ensure these folders are writable:
- `backend/uploads/`
- `library_pdfs/`

### Step 5: Access the System
- **Website:** `http://localhost/st-lawrence-school/frontend/index-redesign.html`
- **Admin Panel:** `http://localhost/st-lawrence-school/backend/admin/login.html`
- **Default Login:** 
  - Username: `admin`
  - Password: `password`

---

## 🔧 Backend Architecture

### Authentication System
- **Session-based authentication**
- Password hashing with `password_hash()` (bcrypt)
- Session timeout after 30 minutes of inactivity
- CSRF protection on forms
- Role-based access control (Admin, Editor, Viewer)

### API Structure
All API endpoints follow REST principles:
- **GET** - Retrieve data
- **POST** - Create/Update data
- **DELETE** - Remove data

### Response Format
```json
{
  "success": true/false,
  "message": "Success/Error message",
  "data": {} // Optional data payload
}
```

### File Upload System
- Validates file types (images: jpg, png, gif; PDFs)
- Limits file size (5MB for images, 10MB for PDFs)
- Generates unique filenames to prevent conflicts
- Stores files in organized folders

---

## 🎨 Frontend Architecture

### Design Principles
- **Mobile-first responsive design**
- **Progressive enhancement**
- **Accessibility compliant** (WCAG 2.1)
- **Performance optimized** (lazy loading, minified assets)

### CSS Architecture
- **BEM methodology** for class naming
- **CSS Grid & Flexbox** for layouts
- **CSS Variables** for theming
- **Media queries** for responsiveness

### JavaScript Patterns
- **ES6+ modules**
- **Event delegation**
- **Async/await** for API calls
- **Error handling** with try-catch

---

## 🤖 AI Chatbot System

### Architecture
The chatbot uses a keyword-matching algorithm with a comprehensive knowledge base.

### Knowledge Base Structure
```php
$knowledge = [
    'category_name' => [
        'keywords' => ['keyword1', 'keyword2', ...],
        'response' => 'Formatted response text'
    ]
];
```

### Matching Algorithm
1. User input is tokenized and cleaned
2. Keywords are matched against knowledge base
3. Scores are calculated based on keyword frequency
4. Best matching category is selected
5. Response is formatted and returned

### Voice Chat Implementation
- **Speech Recognition:** Web Speech API (SpeechRecognition)
- **Text-to-Speech:** Speech Synthesis API (SpeechSynthesisUtterance)
- **Features:**
  - Continuous listening with silence detection
  - Chunked speech output to prevent timeout
  - Mute/unmute functionality
  - Call timer (up to 3 hours)
  - Real-time transcript display
  - Audio wave visualization

### Voice Chat Flow
```
1. User clicks Voice tab
2. System requests microphone permission
3. Speech recognition starts listening
4. User speaks → transcript captured
5. Transcript sent to API
6. API returns response
7. Response converted to speech
8. AI speaks response
9. System resumes listening
10. Repeat steps 4-9
```

---

## 👨‍💼 Admin Panel

### Dashboard
- Total events count
- Total gallery images
- Total teachers
- Total library resources
- Recent admissions
- Quick actions

### Events Management
- Create/Edit/Delete events
- Upload event images
- Set event dates
- View all events in table

### Gallery Management
- Upload images with categories
- Edit image details
- Delete images
- Filter by category

### Teachers Management
- Add teacher profiles
- Upload teacher photos
- Edit teacher information
- Delete teacher profiles

### Library Management
- Upload PDF resources
- Categorize resources
- Edit resource details
- Delete resources

### Admissions Management
- View all applications
- Filter by status (pending/approved/rejected)
- Approve/reject applications
- View application details

---

## 🔐 Authentication & Security

### Security Measures
1. **Password Security**
   - Bcrypt hashing (cost factor: 10)
   - Minimum 8 characters required
   - No plain text storage

2. **Session Security**
   - Secure session cookies
   - Session regeneration on login
   - Automatic timeout (30 minutes)
   - Session hijacking prevention

3. **SQL Injection Prevention**
   - Prepared statements with PDO
   - Parameter binding
   - Input validation

4. **XSS Prevention**
   - Output escaping with `htmlspecialchars()`
   - Content Security Policy headers
   - Input sanitization

5. **CSRF Protection**
   - Token-based validation
   - Token regeneration per request

6. **File Upload Security**
   - File type validation
   - File size limits
   - Unique filename generation
   - Restricted upload directories

---

## 📡 API Documentation

### Chatbot API

#### Endpoint: `/backend/api/chatbot/chat.php`

**Initialize Chat**
```http
POST /backend/api/chatbot/chat.php
Content-Type: application/json

{
  "action": "init"
}

Response:
{
  "success": true,
  "message": "Welcome message",
  "quickActions": ["Question 1", "Question 2", ...]
}
```

**Send Message**
```http
POST /backend/api/chatbot/chat.php
Content-Type: application/json

{
  "action": "chat",
  "message": "What are your school fees?"
}

Response:
{
  "success": true,
  "response": "Formatted response text"
}
```

### Authentication API

#### Login
```http
POST /backend/api/auth/login.php
Content-Type: application/json

{
  "username": "admin",
  "password": "password"
}

Response:
{
  "success": true,
  "message": "Login successful",
  "user": {
    "id": 1,
    "username": "admin",
    "role": "admin"
  }
}
```

#### Logout
```http
POST /backend/api/auth/logout.php

Response:
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Events API

#### Get All Events
```http
GET /backend/api/events/get_events.php

Response:
{
  "success": true,
  "events": [...]
}
```

#### Create Event
```http
POST /backend/api/events/create_event.php
Content-Type: multipart/form-data

{
  "title": "Event Title",
  "description": "Event Description",
  "event_date": "2024-12-25",
  "image": <file>
}

Response:
{
  "success": true,
  "message": "Event created successfully"
}
```

---

## 🌐 Deployment

### Production Checklist
- [ ] Change database credentials
- [ ] Update default admin password
- [ ] Enable HTTPS
- [ ] Set secure session cookies
- [ ] Configure error logging
- [ ] Disable error display
- [ ] Set file permissions (755 for folders, 644 for files)
- [ ] Configure backup system
- [ ] Set up monitoring
- [ ] Test all functionality

### Server Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- SSL certificate (for HTTPS)
- Minimum 512MB RAM
- 1GB disk space

### .htaccess Configuration
```apache
# Enable rewrite engine
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Prevent directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "\.(sql|log|md)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

---

## 🐛 Troubleshooting

### Common Issues

**1. Chatbot not responding**
- Check browser console for errors
- Verify API endpoint URL is correct
- Ensure database connection is working
- Check knowledge_base.php for syntax errors

**2. Voice chat not working**
- Ensure HTTPS is enabled (required for microphone access)
- Check browser compatibility (Chrome, Edge recommended)
- Grant microphone permissions
- Check browser console for errors

**3. File upload failing**
- Check folder permissions (uploads folder must be writable)
- Verify file size limits in php.ini
- Check file type validation
- Ensure disk space is available

**4. Admin login not working**
- Verify database credentials
- Check if users table exists
- Ensure password is hashed correctly
- Clear browser cookies/cache

**5. Images not displaying**
- Check file paths are correct
- Verify images exist in uploads folder
- Check file permissions
- Clear browser cache

### Debug Mode
Enable debug mode in `backend/api/config/database.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

## 📞 Support & Contact

**School Contact:**
- **Phone:** +256 701 420 506 / +256 772 420 506
- **Email:** stlawrencejuniorschoolkabowa@gmail.com
- **Location:** Kabowa, Church Zone, Rubaga Division - Kampala District, Uganda

**Technical Support:**
For technical issues or questions about the system, contact the development team.

---

## 📄 License

This project is proprietary software developed for St. Lawrence Junior School - Kabowa. All rights reserved.

---

## 🙏 Acknowledgments

- St. Lawrence Junior School administration and staff
- Parents and students for feedback
- Development team for implementation

---

**Last Updated:** January 2025
**Version:** 1.0.0
**Status:** Production Ready
#   t e s t 
 
 #   s t - l a w r e n c e - s c h o o l  
 #   s t - l a w r e n c e - s c h o o l  
 #   s t - l a w r e n c e - s c h o o l  
 