// Load dashboard statistics
async function loadStats() {
    try {
        // Load teachers count
        const teachersRes = await fetch(`${API_URL}/teachers/list.php`);
        const teachersData = await teachersRes.json();
        document.getElementById('totalTeachers').textContent = teachersData.count || 0;
        
        // Load events count
        const eventsRes = await fetch(`${API_URL}/events/list.php?upcoming=true`);
        const eventsData = await eventsRes.json();
        document.getElementById('totalEvents').textContent = eventsData.count || 0;
        
        // Load library count
        const libraryRes = await fetch(`${API_URL}/library/list.php`);
        const libraryData = await libraryRes.json();
        document.getElementById('totalLibrary').textContent = libraryData.count || 0;
        
        // Load gallery count
        const galleryRes = await fetch(`${API_URL}/gallery/list.php`);
        const galleryData = await galleryRes.json();
        document.getElementById('totalGallery').textContent = galleryData.count || 0;
        
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Load recent activity
async function loadActivity() {
    const activityList = document.getElementById('activityList');
    activityList.innerHTML = '<p class="loading">Loading activity...</p>';
    
    // Simulated activity for now
    setTimeout(() => {
        activityList.innerHTML = `
            <div class="activity-item">
                <div class="activity-info">
                    <strong>System</strong> - Dashboard loaded successfully
                </div>
                <div class="activity-time">Just now</div>
            </div>
            <div class="activity-item">
                <div class="activity-info">
                    <strong>Welcome!</strong> - Start managing your school website
                </div>
                <div class="activity-time">Today</div>
            </div>
        `;
    }, 500);
}

// Initialize dashboard
if (window.location.pathname.includes('dashboard.html')) {
    loadStats();
    loadActivity();
}
