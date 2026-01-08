// Gallery API Integration

// Load gallery images
async function loadGalleryImages(category = '') {
    const container = document.getElementById('galleryContainer');
    if (!container) return;

    showLoading('galleryContainer');

    let endpoint = '/gallery/list.php';
    if (category) {
        endpoint += `?category=${encodeURIComponent(category)}`;
    }

    const response = await API.get(endpoint);

    if (response.success && response.data && response.data.length > 0) {
        displayGalleryImages(response.data);
    } else if (response.success && response.data.length === 0) {
        showNoData('galleryContainer', 'No images found in this category');
    } else {
        showError('galleryContainer', 'Failed to load gallery images. Please try again.');
    }
}

// Display gallery images
function displayGalleryImages(images) {
    const container = document.getElementById('galleryContainer');
    if (!container) return;

    container.innerHTML = images.map(image => `
        <div class="gallery-item ${image.size}" data-aos="fade-up">
            <img src="../${image.image_url}" 
                 alt="${image.title}" 
                 class="gallery-image"
                 onclick="openLightbox('../${image.image_url}', '${image.title}')">
            <div class="gallery-overlay">
                <h3 class="gallery-title">${image.title}</h3>
                ${image.description ? `<p class="gallery-description">${image.description}</p>` : ''}
                <span class="gallery-category">${image.category}</span>
            </div>
        </div>
    `).join('');
}

// Filter gallery by category
function filterGallery(category) {
    document.querySelectorAll('.gallery-filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    loadGalleryImages(category);
}

// Open lightbox
function openLightbox(imageUrl, title) {
    const lightbox = document.getElementById('lightbox');
    if (!lightbox) {
        // Create lightbox if it doesn't exist
        const lightboxHTML = `
            <div id="lightbox" class="lightbox">
                <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
                <img class="lightbox-image" id="lightboxImage" src="" alt="">
                <div class="lightbox-caption" id="lightboxCaption"></div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', lightboxHTML);
    }

    document.getElementById('lightboxImage').src = imageUrl;
    document.getElementById('lightboxCaption').textContent = title;
    document.getElementById('lightbox').style.display = 'flex';
}

// Close lightbox
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}

// Initialize gallery page
if (window.location.pathname.includes('Gallery-redesign.html')) {
    document.addEventListener('DOMContentLoaded', () => {
        loadGalleryImages();
    });

    // Close lightbox on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
    });
}
