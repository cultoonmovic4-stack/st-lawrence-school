# Security Configuration Guide

## Environment Variables Setup

This project uses environment variables to keep sensitive credentials secure and out of version control.

### Initial Setup

1. **Copy the example environment file:**
   ```bash
   copy .env.example .env
   ```

2. **Edit `.env` with your actual credentials:**
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

3. **NEVER commit `.env` to Git!**
   - The `.env` file is already in `.gitignore`
   - Only commit `.env.example` (without real credentials)

### Getting Gmail App Password

For SMTP to work with Gmail, you need an App Password:

1. Go to your Google Account: https://myaccount.google.com/
2. Select **Security**
3. Under "Signing in to Google," select **2-Step Verification** (enable if not already)
4. At the bottom, select **App passwords**
5. Select **Mail** and **Other (Custom name)**
6. Enter "St. Lawrence School" and click **Generate**
7. Copy the 16-character password
8. Use this password in your `.env` file as `SMTP_PASSWORD`

### For Production Deployment

When deploying to a production server:

1. **DO NOT** upload the `.env` file
2. Create a new `.env` file on the server with production credentials
3. Set appropriate file permissions:
   ```bash
   chmod 600 .env
   ```
4. Use strong, unique passwords for production
5. Consider using server environment variables instead of `.env` file

### Security Checklist

- [ ] `.env` file is in `.gitignore`
- [ ] `.env` file is NOT committed to Git
- [ ] Production uses different credentials than development
- [ ] SMTP password is a Gmail App Password (not your actual Gmail password)
- [ ] Database password is strong and unique
- [ ] File permissions are set correctly (600 for .env)

### Troubleshooting

**Error: ".env file not found"**
- Solution: Copy `.env.example` to `.env` and configure it

**Error: "SMTP authentication failed"**
- Solution: Make sure you're using a Gmail App Password, not your regular password
- Verify 2-Step Verification is enabled on your Google account

**Error: "Database connection failed"**
- Solution: Check your database credentials in `.env`
- Ensure MySQL is running
- Verify the database exists

### What Changed?

**Before (Insecure):**
```php
// Credentials hardcoded in files
$this->mailer->Username = 'cultoonmovic4@gmail.com';
$this->mailer->Password = 'jzzripyjufmicvdg';
```

**After (Secure):**
```php
// Credentials loaded from environment variables
$this->mailer->Username = env('SMTP_USERNAME');
$this->mailer->Password = env('SMTP_PASSWORD');
```

### Files Modified

1. **Created:**
   - `.env` - Your actual credentials (NOT in Git)
   - `.env.example` - Template file (safe to commit)
   - `.gitignore` - Prevents `.env` from being committed
   - `backend/api/config/env.php` - Environment loader

2. **Updated:**
   - `backend/api/config/Email.php` - Now uses env() function
   - `backend/api/config/Database.php` - Now uses env() function

### Need Help?

If you encounter any issues with the security setup, check:
1. `.env` file exists and has correct format
2. No spaces around `=` in `.env` file
3. Values don't have quotes (unless needed)
4. File permissions are correct

---

**Remember:** Security is not optional. Always protect your credentials!
