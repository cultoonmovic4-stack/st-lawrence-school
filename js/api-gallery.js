// Gallery API Integration

// Load gallery images
async function loadGallery() {
    console.log('=== GALLERY LOADING START ===');
    const container = document.getElementById('galleryContainer');
    if (!container) {
        console.error('❌ Gallery container not found!');
        return;
    }
    console.log('✓ Container found:', container);

    try {
        const url = API_BASE_URL + '/gallery/list.php';
        console.log('Fetching from:', url);
        
        const response = await API.get('/gallery/list.php');
        console.log('Response received:', response);
        console.log('Number of images:', response.data ? response.data.length : 0);
        
        if (response.data) {
            console.log('First image data:', response.data[0]);
        }

        if (response.success && response.data && response.data.length > 0) {
            console.log('✓ Success! Displaying', response.data.length, 'images');
            displayGallery(response.data);
        } else {
            console.warn('⚠ No images found in database');
            container.innerHTML = `
                <div style="grid-column: 1/-1; text-align: center; padding: 80px 20px; color: #999;">
                    <i class="fas fa-images" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
                    <h3 style="color: #666; margin-bottom: 10px;">No Gallery Images Yet</h3>
                    <p>Images uploaded through the admin dashboard will appear here.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('❌ Error loading gallery:', error);
        container.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 80px 20px; color: #999;">
                <i class="fas fa-exclamation-circle" style="font-size: 4rem; margin-bottom: 20px; color: #dc3545;"></i>
                <h3 style="color: #666; margin-bottom: 10px;">Error Loading Gallery</h3>
                <p style="color: #dc3545;">${error.message}</p>
            </div>
        `;
    }
    console.log('=== GALLERY LOADING END ===');
}

// Display gallery images
function displayGallery(images) {
    console.log('=== DISPLAY GALLERY START ===');
    console.log('Images to display:', images);
    
    const container = document.getElementById('galleryContainer');
    if (!container) {
        console.error('❌ Container not found!');
        return;
    }

    // Store images globally for lightbox
    window.galleryImages = images;

    // Build HTML - display all images without category grouping
    let html = '';
    
    images.forEach((img, index) => {
        const imagePath = `../backend/${img.image_url}`;
        console.log(`Image ${index}: ${imagePath}`);
        
        html += `
            <div class="gallery-item medium" data-category="${img.category}" data-aos="zoom-in" data-aos-delay="${index * 50}">
                <img src="${imagePath}" alt="${img.title}" loading="lazy" onerror="console.error('Failed to load image:', this.src); this.style.border='3px solid red';">
                <div class="gallery-overlay">
                    <div class="gallery-info">
                        <h4>${img.title}</h4>
                        ${img.description ? `<p>${img.description}</p>` : ''}
                    </div>
                    <button class="view-btn gallery-view-btn" onclick="openAdvancedLightbox(${index})">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>
            </div>
        `;
    });

    console.log('Generated HTML length:', html.length);
    container.innerHTML = html;
    console.log('✓ Gallery HTML inserted into container');
    console.log('=== DISPLAY GALLERY END ===');
    
    // Reinitialize AOS for new elements
    if (typeof AOS !== 'undefined') {
        AOS.refresh();
    }
    
    // Initialize lightbox after images are loaded
    initializeAdvancedLightbox();
    
    // Re-attach filter functionality
    attachFilterListeners();
}

// Attach filter listeners
function attachFilterListeners() {
    const filterButtons = document.querySelectorAll('.dept-filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter gallery items
            galleryItems.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                
                if (category === 'all' || itemCategory === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
}

// Get category icon
function getCategoryIcon(category) {
    const icons = {
        'Academics': 'book',
        'Sports': 'futbol',
        'Events': 'calendar-alt',
        'Facilities': 'building'
    };
    return icons[category] || 'image';
}

// Advanced Lightbox Functions
let currentLightboxIndex = 0;
let lightboxZoom = 1;
let lightboxRotation = 0;
let lightboxIsDragging = false;
let lightboxStartX, lightboxStartY, lightboxTranslateX = 0, lightboxTranslateY = 0;

function openAdvancedLightbox(index) {
    if (!window.galleryImages || window.galleryImages.length === 0) return;
    
    currentLightboxIndex = index;
    const modal = document.getElementById('lightboxModal');
    const image = document.getElementById('lightboxImage');
    const loader = document.getElementById('lightboxLoader');
    
    if (!modal || !image) return;
    
    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Reset transformations
    lightboxZoom = 1;
    lightboxRotation = 0;
    lightboxTranslateX = 0;
    lightboxTranslateY = 0;
    
    // Load image
    updateLightboxImage();
    updateLightboxThumbnails();
    updateLightboxCounter();
}

function updateLightboxImage() {
    const image = document.getElementById('lightboxImage');
    const loader = document.getElementById('lightboxLoader');
    const categorySpan = document.querySelector('#lightboxCategory span');
    const currentImage = window.galleryImages[currentLightboxIndex];
    
    if (!image || !currentImage) return;
    
    // Show loader
    if (loader) loader.style.display = 'flex';
    
    // Update category
    if (categorySpan) categorySpan.textContent = currentImage.category;
    
    // Load new image
    const imageUrl = '../backend/' + currentImage.image_url;
    image.onload = function() {
        if (loader) loader.style.display = 'none';
        applyLightboxTransform();
    };
    image.src = imageUrl;
    image.alt = currentImage.title || currentImage.category;
}

function updateLightboxThumbnails() {
    const thumbnailsContainer = document.getElementById('lightboxThumbnails');
    if (!thumbnailsContainer || !window.galleryImages) return;
    
    thumbnailsContainer.innerHTML = window.galleryImages.map((img, index) => `
        <div class="lightbox-thumbnail ${index === currentLightboxIndex ? 'active' : ''}" onclick="goToLightboxImage(${index})">
            <img src="../backend/${img.image_url}" alt="${img.title || img.category}">
        </div>
    `).join('');
}

function updateLightboxCounter() {
    const counterCurrent = document.querySelector('.counter-current');
    const counterTotal = document.querySelector('.counter-total');
    
    if (counterCurrent) counterCurrent.textContent = currentLightboxIndex + 1;
    if (counterTotal) counterTotal.textContent = window.galleryImages.length;
}

function goToLightboxImage(index) {
    currentLightboxIndex = index;
    lightboxZoom = 1;
    lightboxRotation = 0;
    lightboxTranslateX = 0;
    lightboxTranslateY = 0;
    updateLightboxImage();
    updateLightboxThumbnails();
    updateLightboxCounter();
}

function closeLightbox() {
    const modal = document.getElementById('lightboxModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function nextLightboxImage() {
    if (!window.galleryImages) return;
    currentLightboxIndex = (currentLightboxIndex + 1) % window.galleryImages.length;
    goToLightboxImage(currentLightboxIndex);
}

function prevLightboxImage() {
    if (!window.galleryImages) return;
    currentLightboxIndex = (currentLightboxIndex - 1 + window.galleryImages.length) % window.galleryImages.length;
    goToLightboxImage(currentLightboxIndex);
}

function zoomInLightbox() {
    lightboxZoom = Math.min(lightboxZoom + 0.25, 3);
    applyLightboxTransform();
    updateZoomIndicator();
}

function zoomOutLightbox() {
    lightboxZoom = Math.max(lightboxZoom - 0.25, 0.5);
    applyLightboxTransform();
    updateZoomIndicator();
}

function rotateLightbox() {
    lightboxRotation = (lightboxRotation + 90) % 360;
    applyLightboxTransform();
}

function applyLightboxTransform() {
    const image = document.getElementById('lightboxImage');
    if (image) {
        image.style.transform = `scale(${lightboxZoom}) rotate(${lightboxRotation}deg) translate(${lightboxTranslateX}px, ${lightboxTranslateY}px)`;
    }
}

function updateZoomIndicator() {
    const indicator = document.getElementById('lightboxZoomIndicator');
    const percentage = document.getElementById('zoomPercentage');
    
    if (indicator && percentage) {
        percentage.textContent = Math.round(lightboxZoom * 100) + '%';
        indicator.style.display = 'flex';
        
        setTimeout(() => {
            indicator.style.display = 'none';
        }, 1500);
    }
}

function downloadLightboxImage() {
    const currentImage = window.galleryImages[currentLightboxIndex];
    if (!currentImage) return;
    
    const link = document.createElement('a');
    link.href = '../backend/' + currentImage.image_url;
    link.download = currentImage.title || 'gallery-image';
    link.click();
}

function toggleShareMenu() {
    const shareMenu = document.getElementById('lightboxShareMenu');
    console.log('Toggle share menu clicked', shareMenu);
    if (shareMenu) {
        const isActive = shareMenu.classList.contains('active');
        console.log('Share menu active:', isActive);
        shareMenu.classList.toggle('active');
        console.log('Share menu classes after toggle:', shareMenu.className);
    } else {
        console.error('Share menu element not found!');
    }
}

function shareImage(platform) {
    const currentImage = window.galleryImages[currentLightboxIndex];
    if (!currentImage) return;
    
    const imageUrl = window.location.origin + window.location.pathname.replace('Gallery-redesign.html', '') + '../backend/' + currentImage.image_url;
    const title = currentImage.title || 'St. Lawrence Gallery Image';
    const text = `Check out this image from St. Lawrence Junior School: ${title}`;
    
    let shareUrl = '';
    
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(imageUrl)}`;
            window.open(shareUrl, '_blank', 'width=600,height=400');
            break;
            
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(imageUrl)}`;
            window.open(shareUrl, '_blank', 'width=600,height=400');
            break;
            
        case 'pinterest':
            shareUrl = `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(imageUrl)}&description=${encodeURIComponent(text)}`;
            window.open(shareUrl, '_blank', 'width=600,height=400');
            break;
            
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + imageUrl)}`;
            window.open(shareUrl, '_blank');
            break;
            
        case 'copy':
            navigator.clipboard.writeText(imageUrl).then(() => {
                alert('Image link copied to clipboard!');
            }).catch(() => {
                prompt('Copy this link:', imageUrl);
            });
            break;
    }
    
    toggleShareMenu();
}

function toggleFullscreen() {
    const modal = document.getElementById('lightboxModal');
    if (!modal) return;
    
    if (!document.fullscreenElement) {
        modal.requestFullscreen().catch(err => {
            console.log('Fullscreen error:', err);
        });
    } else {
        document.exitFullscreen();
    }
}

function initializeAdvancedLightbox() {
    // Close button
    const closeBtn = document.getElementById('lightboxClose');
    if (closeBtn) closeBtn.onclick = closeLightbox;
    
    // Navigation buttons
    const prevBtn = document.getElementById('lightboxPrev');
    const nextBtn = document.getElementById('lightboxNext');
    if (prevBtn) prevBtn.onclick = prevLightboxImage;
    if (nextBtn) nextBtn.onclick = nextLightboxImage;
    
    // Action buttons
    const zoomInBtn = document.getElementById('lightboxZoomIn');
    const zoomOutBtn = document.getElementById('lightboxZoomOut');
    const rotateBtn = document.getElementById('lightboxRotate');
    const downloadBtn = document.getElementById('lightboxDownload');
    const shareBtn = document.getElementById('lightboxShare');
    const fullscreenBtn = document.getElementById('lightboxFullscreen');
    
    console.log('Lightbox buttons found:', {
        zoomIn: !!zoomInBtn,
        zoomOut: !!zoomOutBtn,
        rotate: !!rotateBtn,
        download: !!downloadBtn,
        share: !!shareBtn,
        fullscreen: !!fullscreenBtn
    });
    
    if (zoomInBtn) zoomInBtn.onclick = zoomInLightbox;
    if (zoomOutBtn) zoomOutBtn.onclick = zoomOutLightbox;
    if (rotateBtn) rotateBtn.onclick = rotateLightbox;
    if (downloadBtn) downloadBtn.onclick = downloadLightboxImage;
    if (shareBtn) {
        shareBtn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Share button clicked!');
            toggleShareMenu();
        };
    }
    if (fullscreenBtn) fullscreenBtn.onclick = toggleFullscreen;
    
    // Share menu options
    const shareOptions = document.querySelectorAll('.share-option');
    shareOptions.forEach(option => {
        option.onclick = function() {
            const platform = this.getAttribute('data-platform');
            shareImage(platform);
        };
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('lightboxModal');
        if (!modal || !modal.classList.contains('active')) return;
        
        if (e.key === 'Escape') closeLightbox();
        else if (e.key === 'ArrowLeft') prevLightboxImage();
        else if (e.key === 'ArrowRight') nextLightboxImage();
        else if (e.key === '+' || e.key === '=') zoomInLightbox();
        else if (e.key === '-') zoomOutLightbox();
    });
    
    // Click backdrop to close
    const backdrop = document.querySelector('.lightbox-backdrop');
    if (backdrop) backdrop.onclick = closeLightbox;
    
    console.log('✓ Advanced lightbox initialized with share functionality');
}

// Old simple lightbox - removed
function openLightbox(imageUrl, title) {
    // This function is no longer used
}

// Initialize gallery page
if (window.location.pathname.includes('Gallery-redesign.html')) {
    document.addEventListener('DOMContentLoaded', () => {
        loadGallery();
    });
}
