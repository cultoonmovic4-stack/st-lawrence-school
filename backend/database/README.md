# DATABASE SETUP GUIDE

## 🗄️ St. Lawrence School Database

Complete database schema with 29 tables for school management system.

---

## 📋 INSTALLATION

### **Method 1: Using phpMyAdmin (Recommended)**

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Click "Choose File"
4. Select: `CREATE_ALL_TABLES.sql`
5. Click "Go" button
6. Wait for success message

### **Method 2: Using MySQL Command Line**

```bash
mysql -u root -p < CREATE_ALL_TABLES.sql
```

---

## ✅ WHAT GETS CREATED

### **Database:**
- Name: `st_lawrence_school`
- Character Set: UTF-8
- Collation: utf8_general_ci

### **Tables: 29**

**Core Tables (9):**
1. users
2. teachers
3. gallery_images
4. library_resources
5. events
6. testimonials
7. contact_submissions
8. admission_applications
9. calendar_events

**Enhancement Tables (20):**
10. admin_activity_logs
11. file_uploads
12. email_logs
13. notifications
14. settings
15. departments
16. subjects
17. application_status_history
18. download_logs
19. page_views
20. backup_logs
21. teacher_subjects
22. gallery_tags
23. event_attendees
24. testimonial_ratings
25. contact_replies
26. sessions
27. password_resets
28. api_keys
29. search_logs

### **Default Data:**

**Admin User:**
- Username: `admin`
- Email: `admin@stlawrence.com`
- Password: `password` (hashed)
- Role: Admin
- Status: Active

---

## 🔍 VERIFY INSTALLATION

After running the SQL file, verify:

1. **Check Database Exists:**
```sql
SHOW DATABASES LIKE 'st_lawrence_school';
```

2. **Check Tables Created:**
```sql
USE st_lawrence_school;
SHOW TABLES;
```
Should show 29 tables.

3. **Check Admin User:**
```sql
SELECT * FROM users WHERE email = 'admin@stlawrence.com';
```
Should return 1 row.

---

## 🔧 CONFIGURATION

After installation, update database credentials in:

**File:** `backend/api/config/database.php`

```php
private $host = "localhost";
private $db_name = "st_lawrence_school";
private $username = "root";
private $password = "";  // Change if you have MySQL password
```

---

## 🔒 SECURITY NOTES

1. **Change Default Password:**
   - Login to admin panel
   - Change password from default `password`

2. **Database User:**
   - For production, create dedicated MySQL user
   - Don't use `root` in production

3. **Backup:**
   - Regular backups recommended
   - Use phpMyAdmin Export feature

---

## 📊 DATABASE SIZE

- **Empty Database:** ~50KB
- **With Sample Data:** ~500KB
- **Expected Growth:** ~10MB per year

---

## 🐛 TROUBLESHOOTING

### **Error: Database already exists**
```sql
DROP DATABASE st_lawrence_school;
```
Then run the SQL file again.

### **Error: Access denied**
- Check MySQL is running in XAMPP
- Verify username/password
- Check user has CREATE DATABASE permission

### **Error: Table already exists**
- Drop database first
- Or manually drop each table
- Then run SQL file again

---

## 📝 TABLE RELATIONSHIPS

```
users
  ├── admin_activity_logs (user_id)
  ├── file_uploads (uploaded_by)
  ├── notifications (user_id)
  ├── sessions (user_id)
  └── password_resets (user_id)

teachers
  ├── departments (head_teacher_id)
  └── teacher_subjects (teacher_id)

subjects
  └── teacher_subjects (subject_id)

gallery_images
  └── gallery_tags (gallery_id)

library_resources
  └── download_logs (library_id)

events
  └── event_attendees (event_id)

testimonials
  └── testimonial_ratings (testimonial_id)

contact_submissions
  └── contact_replies (contact_id)

admission_applications
  └── application_status_history (application_id)
```

---

## 🎯 NEXT STEPS

After database setup:

1. ✅ Verify admin user exists
2. ✅ Test login at admin panel
3. ✅ Add sample teachers
4. ✅ Upload test images
5. ✅ Create test events

---

**Database Version:** 1.0  
**Last Updated:** January 8, 2026  
**Status:** Production Ready ✅
