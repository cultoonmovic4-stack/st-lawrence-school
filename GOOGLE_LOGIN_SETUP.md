# Google Login Setup Guide
## St. Lawrence Junior School - Admin Portal

## Overview
This guide will help you set up Google OAuth authentication for the admin login page.

---

## Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click "Select a project" → "New Project"
3. Enter project name: `St Lawrence School Admin`
4. Click "Create"

---

## Step 2: Enable Google Sign-In API

1. In your project, go to "APIs & Services" → "Library"
2. Search for "Google+ API" or "Google Identity"
3. Click "Enable"

---

## Step 3: Create OAuth 2.0 Credentials

1. Go to "APIs & Services" → "Credentials"
2. Click "Create Credentials" → "OAuth client ID"
3. If prompted, configure OAuth consent screen:
   - User Type: **External**
   - App name: `St. Lawrence School Admin`
   - User support email: `cultoonmovic4@gmail.com`
   - Developer contact: `cultoonmovic4@gmail.com`
   - Click "Save and Continue"
   - Scopes: Add `email` and `profile`
   - Click "Save and Continue"
   - Test users: Add your admin emails
   - Click "Save and Continue"

4. Create OAuth Client ID:
   - Application type: **Web application**
   - Name: `St Lawrence Admin Login`
   - Authorized JavaScript origins:
     - `https://cultoonmovic4-stack.github.io` (for GitHub Pages)
     - For localhost testing, you'll need to use `http://localhost:8080` or similar
     - **Note**: Google doesn't allow plain `http://localhost` - you need a port number
   - Authorized redirect URIs:
     - `https://cultoonmovic4-stack.github.io/st-lawrence-school/backend/admin/login.html`
   - Click "Create"

5. **Copy your Client ID** - You'll need this!

---

## For GitHub Pages (Recommended for Testing)

Since Google requires public domains, use your GitHub Pages URL:

**Authorized JavaScript origins:**
```
https://cultoonmovic4-stack.github.io
```

**Authorized redirect URIs:**
```
https://cultoonmovic4-stack.github.io/st-lawrence-school/backend/admin/login.html
```

---

## For Localhost Testing (Alternative)

If you need to test locally, you have two options:

### Option 1: Use a port number
```
http://localhost:8080
http://localhost:3000
http://127.0.0.1:8080
```

### Option 2: Use a local domain service
- Use services like `ngrok` or `localtunnel` to create a public URL
- Example: `https://abc123.ngrok.io`

---

## Step 5: Update Login Page

1. Open `backend/admin/login.html`
2. Find this line (around line 520):
   ```javascript
   client_id: 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com',
   ```
3. Replace `YOUR_GOOGLE_CLIENT_ID` with your actual Client ID from Step 3

Example:
```javascript
client_id: '123456789-abcdefghijklmnop.apps.googleusercontent.com',
```

---

## Step 6: Update Database Schema (If Needed)

Make sure your `users` table has these columns:

```sql
ALTER TABLE users 
ADD COLUMN google_id VARCHAR(255) NULL,
ADD COLUMN profile_picture VARCHAR(500) NULL;
```

If the table doesn't exist, create it:

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NULL,
    google_id VARCHAR(255) NULL,
    profile_picture VARCHAR(500) NULL,
    role ENUM('admin', 'teacher', 'viewer') DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## Step 7: Test Google Login

**On GitHub Pages:**
1. Push your code to GitHub
2. Wait for GitHub Pages to deploy (2-3 minutes)
3. Open: `https://cultoonmovic4-stack.github.io/st-lawrence-school/backend/admin/login.html`
4. Click the "Google" button
5. Sign in with your Google account
6. You should be redirected to the dashboard

**Note:** Google login will work on GitHub Pages but NOT on localhost without proper configuration.

---

## How It Works

1. **User clicks "Google" button** → Triggers Google Sign-In popup
2. **User signs in with Google** → Google returns a JWT token
3. **Token sent to backend** → `backend/api/auth/google-login.php` verifies the token
4. **User verified** → Creates/updates user in database
5. **Session created** → User logged in and redirected to dashboard

---

## Security Notes

- ✅ Google tokens are verified server-side
- ✅ Email must be verified by Google
- ✅ Token expiration is checked
- ✅ New users are created with 'viewer' role by default
- ✅ Sessions are used to maintain login state

---

## Default User Roles

When a user logs in with Google for the first time:
- **Role**: `viewer` (limited access)
- **Admin must upgrade** their role to `admin` or `teacher` in the database

To make a Google user an admin:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@gmail.com';
```

---

## Troubleshooting

### "Invalid Google token" error
- Check that your Client ID is correct
- Verify authorized origins in Google Console
- Make sure the token hasn't expired

### "User not found" error
- Check database connection
- Verify `users` table exists
- Check that email column is unique

### Google popup doesn't appear
- Check browser console for errors
- Verify Google Sign-In script is loaded
- Check that Client ID is valid

---

## Production Deployment

When deploying to production:

1. Add your production domain to Google Console:
   - Authorized JavaScript origins: `https://yourdomain.com`
   - Authorized redirect URIs: `https://yourdomain.com/backend/admin/login.html`

2. Update the Client ID in `login.html` if different

3. Test thoroughly before going live

---

## Support

For issues or questions:
- Email: cultoonmovic4@gmail.com
- Check Google Cloud Console for API errors
- Review browser console for JavaScript errors

---

**Your Google login is now ready to use!** 🎉
