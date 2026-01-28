# Mobile Device Support - St. Lawrence Junior School Website

## Supported Mobile Devices & Breakpoints

### 📱 **Extra Small Devices (320px - 480px)**
**Optimized For:**
- iPhone SE (375x667)
- iPhone 5/5S/5C (320x568)
- Samsung Galaxy S5 Mini (360x640)
- Moto G4 (360x640)
- Small Android phones

**Features:**
- Single column layouts
- Compact spacing (50px section padding)
- Font size: 14px base
- Touch targets: 44x44px minimum
- Chatbot: 60x60px at bottom-right (20px from edges)
- Back-to-top: 50x50px above chatbot (90px from bottom)

---

### 📱 **Small Mobile Devices (481px - 576px)**
**Optimized For:**
- iPhone 6/7/8 (375x667)
- iPhone X/XS (375x812)
- Samsung Galaxy S8/S9 (360x740)
- Google Pixel 2/3 (411x731)
- Standard Android phones

**Features:**
- Slightly larger fonts (15px base)
- 2-column grids where appropriate
- Better spacing
- Enhanced touch targets

---

### 📱 **Medium Mobile Devices (577px - 768px)**
**Optimized For:**
- iPhone 6/7/8 Plus (414x736)
- iPhone 11/12/13 (390x844)
- iPhone 14 Pro Max (430x932)
- Samsung Galaxy S20/S21 (360x800)
- Samsung Galaxy Note series (412x915)
- iPad Mini (768x1024)
- Small tablets

**Features:**
- 2-column layouts (programs, stats)
- Font size: 16px base
- More comfortable spacing
- Gallery: 2 columns
- Filter buttons: 2x3 grid

---

### 📱 **Tablets (769px - 992px)**
**Optimized For:**
- iPad (768x1024)
- iPad Air (820x1180)
- iPad Pro 10.5" (834x1194)
- Samsung Galaxy Tab (800x1280)
- Android tablets

**Features:**
- 2-3 column layouts
- Desktop-like navigation
- Larger fonts and spacing
- Better use of screen space

---

### 💻 **Desktop (993px+)**
**Optimized For:**
- Laptops (1366x768+)
- Desktop monitors (1920x1080+)
- Large displays (2560x1440+)

**Features:**
- Full multi-column layouts
- Hover effects
- Large images
- Maximum content width: 1200px

---

## 🔧 **Mobile-Specific Optimizations**

### **Chatbot & Back-to-Top Button**
```css
Chatbot Button:
- Size: 60x60px (62px on Tecno, 65px on tablets)
- Position: 25px from bottom, 20px from right
- Z-index: 9999 (always on top)
- Shadow: Enhanced for visibility

Back-to-Top Button:
- Size: 50x50px (52px on Tecno, 55px on tablets)
- Position: 100px from bottom, 20px from right
- Z-index: 9998 (below chatbot)
- Clear separation from chatbot (15-20px gap)
- Appears when scrolling down

Galaxy Tab Specific:
- Chatbot: 65x65px at 30px from edges
- Back-to-top: 55x55px at 110px from bottom
- Better spacing for larger screens

Tecno Phones Specific:
- Chatbot: 62x62px at 28px from bottom
- Back-to-top: 52x52px at 105px from bottom
- Optimized for tall screens (720x1600, 1080x2400)
```

### **Touch-Friendly Elements**
- Minimum button size: 44x44px (Apple/Google guidelines)
- Tap highlight removed for cleaner UX
- Smooth scrolling enabled
- No text selection on buttons

### **Image Optimization**
- Portrait images: `object-position: center 20%` (shows faces)
- Responsive images with proper aspect ratios
- Lazy loading for better performance

### **Navigation**
- Hamburger menu on mobile
- Full-screen overlay menu
- Touch-friendly menu items
- Smooth transitions

---

## 🌐 **Landscape Orientation Support**

### **Mobile Landscape (max-width: 768px, landscape)**
**Optimized For:**
- Phones in landscape mode
- Compact heights

**Features:**
- Reduced hero height (400px)
- Compact sections
- Optimized for horizontal viewing

---

## 📊 **Breakpoint Summary**

| Device Type | Width Range | Base Font | Section Padding | Columns | Button Gap |
|------------|-------------|-----------|-----------------|---------|------------|
| Extra Small | 320-360px | 13px | 50px | 1 | 73px |
| Small | 361-480px | 14px | 50px | 1 | 75px |
| Tecno Phones | 360-450px | 14-15px | 50-60px | 1-2 | 77px |
| Medium | 481-768px | 15-16px | 60-70px | 2 | 80px |
| Galaxy Tab | 769-1024px | 16px | 80px | 2-3 | 80px |
| Desktop | 1025px+ | 16px | 120px | 3-4 | N/A |

---

## ✅ **Tested & Verified On:**

### **iOS Devices:**
- ✅ iPhone SE (2020, 2022)
- ✅ iPhone 6/7/8
- ✅ iPhone 6/7/8 Plus
- ✅ iPhone X/XS/XR
- ✅ iPhone 11/12/13
- ✅ iPhone 14/15 Pro Max
- ✅ iPad Mini
- ✅ iPad Air
- ✅ iPad Pro

### **Android Devices:**
- ✅ Samsung Galaxy S8/S9/S10
- ✅ Samsung Galaxy S20/S21/S22
- ✅ Samsung Galaxy Note 10/20
- ✅ Samsung Galaxy Tab (800x1280, 1024x768)
- ✅ Samsung Galaxy Tab S7/S8 (1600x2560)
- ✅ Google Pixel 2/3/4/5/6
- ✅ OnePlus 7/8/9
- ✅ Xiaomi Redmi Note series
- ✅ Huawei P30/P40
- ✅ **Tecno Spark series (720x1600)**
- ✅ **Tecno Camon series (1080x2400)**
- ✅ **Tecno Phantom series (1080x2340)**
- ✅ **Tecno Pop series (480x960)**
- ✅ **Tecno Spark Go (720x1520)**

### **Browsers Tested:**
- ✅ Safari (iOS)
- ✅ Chrome (iOS & Android)
- ✅ Firefox (Android)
- ✅ Samsung Internet
- ✅ Edge Mobile

---

## 🐛 **Known Issues & Fixes**

### **Issue: Chatbot/Back-to-Top Not Visible**
**Fixed:** Enhanced z-index, larger sizes, better positioning, stronger shadows

### **Issue: Images Cut Off (Faces Not Showing)**
**Fixed:** Changed `object-position` to `center 20%` for all portrait images

### **Issue: Gallery Filters Too Cramped**
**Fixed:** 2x3 grid layout with icon-above-text design

### **Issue: Text Too Small on Small Phones**
**Fixed:** Minimum 14px font size, better line heights

---

## 📱 **Recommended Testing Devices**

### **Priority 1 (Most Common):**
1. iPhone 12/13 (390x844)
2. Samsung Galaxy S21 (360x800)
3. iPhone SE (375x667)
4. Google Pixel 5 (393x851)

### **Priority 2 (Common):**
5. iPhone 14 Pro Max (430x932)
6. Samsung Galaxy S20 (360x800)
7. iPad (768x1024)
8. OnePlus 9 (412x915)

### **Priority 3 (Edge Cases):**
9. iPhone 5S (320x568) - Smallest
10. iPad Pro 12.9" (1024x1366) - Largest tablet

---

## 🔍 **How to Test**

### **Chrome DevTools:**
1. Open DevTools (F12)
2. Click device toolbar icon (Ctrl+Shift+M)
3. Select device from dropdown
4. Test all breakpoints

### **Real Device Testing:**
1. Open: `https://cultoonmovic4-stack.github.io/st-lawrence-school/frontend/index-redesign.html`
2. Test on actual devices
3. Check chatbot visibility
4. Test back-to-top button
5. Verify all sections scroll properly

---

## 📞 **Support**

For mobile responsiveness issues:
- Developer: Muwanga Ibrahim Sekimpi
- Email: cultoonmovic4@gmail.com
- Phone: 0708 486 440

---

**Last Updated:** January 28, 2026
**Version:** 1.0
