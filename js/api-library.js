// Library API Integration

// Load library resources
async function loadLibraryResources(classLevel = '', subject = '') {
    const container = document.getElementById('libraryContainer');
    if (!container) return;

    showLoading('libraryContainer');

    let endpoint = '/library/list.php?';
    if (classLevel) endpoint += `class_level=${encodeURIComponent(classLevel)}&`;
    if (subject) endpoint += `subject=${encodeURIComponent(subject)}&`;

    const response = await API.get(endpoint);

    if (response.success && response.data && response.data.length > 0) {
        displayLibraryResources(response.data);
    } else if (response.success && response.data.length === 0) {
        showNoData('libraryContainer', 'No resources found for this selection');
    } else {
        showError('libraryContainer', 'Failed to load library resources. Please try again.');
    }
}

// Display library resources
function displayLibraryResources(resources) {
    const container = document.getElementById('libraryContainer');
    if (!container) return;

    container.innerHTML = resources.map(resource => `
        <div class="pdf-card" data-aos="fade-up">
            <div class="pdf-icon">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div class="pdf-info">
                <h3 class="pdf-title">${resource.title}</h3>
                ${resource.description ? `<p class="pdf-description">${resource.description}</p>` : ''}
                <div class="pdf-meta">
                    <span class="pdf-class"><i class="fas fa-graduation-cap"></i> ${resource.class_level}</span>
                    <span class="pdf-subject"><i class="fas fa-book"></i> ${resource.subject}</span>
                    <span class="pdf-size"><i class="fas fa-file"></i> ${formatFileSize(resource.file_size)}</span>
                </div>
                <div class="pdf-stats">
                    <span class="pdf-downloads"><i class="fas fa-download"></i> ${resource.download_count} downloads</span>
                    <span class="pdf-date"><i class="fas fa-calendar"></i> ${formatDate(resource.created_at)}</span>
                </div>
            </div>
            <div class="pdf-actions">
                <a href="../${resource.file_path}" 
                   target="_blank" 
                   class="btn-view"
                   onclick="trackDownload(${resource.id})">
                    <i class="fas fa-eye"></i> View
                </a>
                <a href="../${resource.file_path}" 
                   download 
                   class="btn-download"
                   onclick="trackDownload(${resource.id})">
                    <i class="fas fa-download"></i> Download
                </a>
            </div>
        </div>
    `).join('');
}

// Track PDF download
async function trackDownload(resourceId) {
    await API.get(`/library/download.php?id=${resourceId}`);
}

// Filter library by class
function filterLibraryByClass(classLevel) {
    document.querySelectorAll('.class-filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    const subject = document.getElementById('subjectFilter')?.value || '';
    loadLibraryResources(classLevel, subject);
}

// Filter library by subject
function filterLibraryBySubject() {
    const subject = document.getElementById('subjectFilter').value;
    const activeClass = document.querySelector('.class-filter-btn.active');
    const classLevel = activeClass ? activeClass.dataset.class : '';
    
    loadLibraryResources(classLevel, subject);
}

// Search library
function searchLibrary() {
    const searchInput = document.getElementById('librarySearch');
    if (!searchInput) return;

    const searchTerm = searchInput.value.toLowerCase();
    const pdfCards = document.querySelectorAll('.pdf-card');

    pdfCards.forEach(card => {
        const title = card.querySelector('.pdf-title').textContent.toLowerCase();
        const description = card.querySelector('.pdf-description')?.textContent.toLowerCase() || '';

        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Helper: Format file size
function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

// Helper: Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

// Initialize library page
if (window.location.pathname.includes('Library-redesign.html')) {
    document.addEventListener('DOMContentLoaded', () => {
        loadLibraryResources();
    });
}
