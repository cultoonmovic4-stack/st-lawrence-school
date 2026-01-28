# Stock Images to Replace with Real School Images

## Homepage (frontend/index-redesign.html)

### 1. **School Logo** (Used in 3 places)
- **Current**: `img/5.jpg`
- **Locations**:
  - Line 44: Page loader logo
  - Line 63: Header/navbar logo
  - Line 1095: Footer logo
- **Recommended size**: 200px × 200px (square)
- **Your replacement**: Use your actual school logo/badge

### 2. **Hero Section Background Videos**
- **Current**: 
  - `img/students walking.mp4`
  - `img/Welcome to Cambridge!.mp4`
- **Location**: Line 150-151 (Hero section)
- **Recommended**: 1920×1080 or 1280×720 video of your school
- **Your replacement**: Video of your students/school campus

### 3. **About Section Image**
- **Current**: `img/new 4.JPG` (this is YOUR image but not loading due to cache)
- **Location**: Line 262
- **Size**: 11.6MB (TOO LARGE - compress to under 500KB)
- **Recommended**: 1200px × 800px landscape image
- **Suggestion**: School building, classroom, or students learning

### 4. **Footer Background Video**
- **Current**: `img/students walking.mp4`
- **Location**: Line 1086
- **Same as hero video**

### 5. **World Map Background** (CSS)
- **Current**: `img/world map.jpg`
- **Location**: `css/redesign-style.css` line 13942
- **Used in**: Contact section or global reach section
- **Your replacement**: Keep as is OR use school campus aerial view

---

## Other Pages to Check

### Teachers Page (frontend/Teachers-redesign.html)
- Teacher photos loaded dynamically from database
- Default fallback: `img/user.jpg`
- **Action**: Upload real teacher photos via admin panel

### Gallery Page (frontend/Gallery-redesign.html)
- Gallery images loaded dynamically from database
- **Action**: Upload real school event photos via admin panel

### About Page (frontend/About-redesign.html)
- Director's photo
- School history images
- **Action**: Check this page for stock images

---

## Summary of Images YOU Need to Replace

| Image Type | Current File | Where Used | Recommended Size | Priority |
|------------|-------------|------------|------------------|----------|
| School Logo | `img/5.jpg` | Header, Footer, Loader | 200×200px | HIGH |
| Hero Video | `img/students walking.mp4` | Hero section | 1920×1080 | HIGH |
| About Image | `img/new 4.JPG` | About section | 1200×800px | HIGH |
| World Map | `img/world map.jpg` | Contact/CSS | 1920×1080 | LOW |

---

## How to Replace Images

### Method 1: Direct Replacement (Easiest)
1. Rename your school logo to `5.jpg`
2. Replace the file in `img/` folder
3. Clear browser cache: Ctrl + Shift + Delete
4. Hard refresh: Ctrl + F5

### Method 2: Update HTML (Recommended)
1. Keep your images with descriptive names (e.g., `school-logo.jpg`, `hero-video.mp4`)
2. Update the HTML file paths
3. Add version parameter: `?v=2` to force reload

### Method 3: Compress Large Images First
Your `new 4.JPG` is 11.6MB - this is why it's not loading!

**Compress it:**
- Go to https://tinyjpg.com
- Upload `new 4.JPG`
- Download compressed version (should be ~300KB)
- Replace the original file

---

## Current Issue with "new 4.JPG"

The image IS in the HTML correctly, but:
1. **File is 11.6MB** - too large for web (should be under 500KB)
2. **Browser cached the old stock image** - need to clear cache
3. **File name has a space** - `new 4.JPG` (works but not ideal)

**Quick Fix:**
1. Compress the image to under 500KB
2. Rename to `about-school.jpg` (no spaces)
3. Update HTML line 262 to: `src="../img/about-school.jpg?v=2"`
4. Clear browser cache and refresh

---

## Need Help?

Let me know which images you want to replace and I'll update the HTML/CSS files for you!
