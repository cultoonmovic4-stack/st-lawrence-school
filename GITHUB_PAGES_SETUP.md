# GitHub Pages Setup Guide
## St. Lawrence Junior School Website

## ⚠️ IMPORTANT LIMITATIONS

GitHub Pages **ONLY** works with static files (HTML, CSS, JavaScript). Your PHP backend and database features **WILL NOT WORK**.

### ✅ What WILL Work:
- All frontend pages (HTML/CSS/JS)
- Navigation between pages
- Image galleries
- Basic chatbot UI (visual only)
- Voice recognition (browser feature)
- Responsive design
- Animations

### ❌ What WON'T Work:
- PHP backend (all `.php` files)
- MySQL database queries
- Chatbot responses from database
- Contact form submissions
- Email sending
- Admin panel
- Teacher/Gallery/Library management
- Any server-side processing

---

## Enable GitHub Pages

### Step 1: Go to Repository Settings
1. Open your browser
2. Go to: https://github.com/cultoonmovic4-stack/st-lawrence-school
3. Click the "Settings" tab (top right)

### Step 2: Navigate to Pages
1. In the left sidebar, scroll down
2. Click "Pages" (under "Code and automation")

### Step 3: Configure Source
1. Under "Build and deployment"
2. Under "Source", select: **Deploy from a branch**
3. Under "Branch":
   - Select: **main**
   - Select folder: **/ (root)**
4. Click "Save"

### Step 4: Wait for Deployment
1. GitHub will start building your site (takes 1-3 minutes)
2. Refresh the page after a minute
3. You'll see a message: "Your site is live at..."

### Step 5: Access Your Live Website
Your website will be available at:
```
https://cultoonmovic4-stack.github.io/st-lawrence-school/
```

---

## What Visitors Will See

When people visit your GitHub Pages site, they will see:
- ✅ Beautiful homepage with school information
- ✅ About page, Teachers page, Gallery
- ✅ Admission forms (but submissions won't work)
- ✅ Contact page (but form won't send emails)
- ✅ Library page (but can't download PDFs)
- ✅ All images and videos
- ❌ Chatbot will show UI but won't respond (no database)
- ❌ Admin panel won't work (needs PHP)

---

## For Full Functionality

If you want ALL features to work (chatbot, forms, admin panel, database), you MUST use:
- **InfinityFree** (free PHP hosting) - See `INFINITYFREE_DEPLOYMENT.md`
- **Paid hosting** (Hostinger, Bluehost, etc.)
- **Your own server** (XAMPP on a public server)

GitHub Pages is great for **showcasing the design** but not for **full functionality**.

---

## Troubleshooting

### Site Not Loading?
- Wait 3-5 minutes after enabling Pages
- Check Settings > Pages for deployment status
- Look for green checkmark next to deployment

### 404 Error?
- Make sure you selected "main" branch
- Make sure you selected "/ (root)" folder
- Check that `index.html` exists in root

### Images Not Loading?
- All paths in HTML files use relative paths (`../img/...`)
- This should work automatically on GitHub Pages

### Chatbot Not Responding?
- This is expected - chatbot needs PHP backend and database
- Only the UI will show, no actual responses

---

## Share Your Website

Once live, share this link:
```
https://cultoonmovic4-stack.github.io/st-lawrence-school/
```

People will see the beautiful website design, but dynamic features won't work.

---

## Next Steps

1. Enable GitHub Pages (follow steps above)
2. Wait 2-3 minutes
3. Visit your live site
4. Share the link!

If you need full functionality later, consider InfinityFree or paid hosting.
