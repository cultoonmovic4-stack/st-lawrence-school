// Teachers API Integration

// Load all teachers
async function loadTeachers(department = '') {
    const container = document.getElementById('teachersContainer');
    if (!container) return;

    showLoading('teachersContainer');

    let endpoint = '/teachers/list.php';
    if (department) {
        endpoint += `?department=${encodeURIComponent(department)}`;
    }

    const response = await API.get(endpoint);

    if (response.success && response.data && response.data.length > 0) {
        displayTeachers(response.data);
    } else if (response.success && response.data.length === 0) {
        showNoData('teachersContainer', 'No teachers found in this department');
    } else {
        showError('teachersContainer', 'Failed to load teachers. Please try again.');
    }
}

// Display teachers
function displayTeachers(teachers) {
    const container = document.getElementById('teachersContainer');
    if (!container) return;

    container.innerHTML = teachers.map(teacher => {
        // Handle photo URL properly - check for null, empty, or invalid values
        let photoUrl = '../img/user.jpg';
        if (teacher.photo_url && teacher.photo_url !== 'null' && teacher.photo_url.trim() !== '') {
            photoUrl = `../backend/${teacher.photo_url}`;
        }
        
        console.log('Teacher:', teacher.name, 'Photo URL:', photoUrl);
        
        // Get department badge class
        const deptClass = teacher.department ? teacher.department.toLowerCase().replace(/\s+/g, '-') : 'general';
        
        return `
        <div class="teacher-card" data-department="${teacher.department ? teacher.department.toLowerCase() : 'general'}" data-aos="fade-up">
            <div class="teacher-image">
                <img src="${photoUrl}" 
                     alt="${teacher.name}" 
                     onerror="this.src='../img/user.jpg'">
                <div class="teacher-overlay">
                    <div class="teacher-overlay-title">Connect with ${teacher.name}</div>
                    <div class="teacher-social">
                        ${teacher.facebook ? `<a href="${teacher.facebook}" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>` : ''}
                        ${teacher.twitter ? `<a href="${teacher.twitter}" target="_blank" title="Twitter"><i class="fab fa-twitter"></i></a>` : ''}
                        ${teacher.linkedin ? `<a href="${teacher.linkedin}" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>` : ''}
                        ${teacher.email ? `<a href="mailto:${teacher.email}" title="Email"><i class="fas fa-envelope"></i></a>` : ''}
                        ${teacher.phone ? `<a href="tel:${teacher.phone}" title="Call"><i class="fas fa-phone"></i></a>` : ''}
                    </div>
                    <div class="teacher-contact-overlay">
                        ${teacher.phone ? `
                        <div class="teacher-contact-item">
                            <i class="fas fa-phone"></i>
                            <span>${teacher.phone}</span>
                        </div>` : ''}
                        ${teacher.email ? `
                        <div class="teacher-contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>${teacher.email}</span>
                        </div>` : ''}
                        <div class="teacher-contact-item">
                            <i class="fas fa-clock"></i>
                            <span>Mon-Fri: 8:00 AM - 5:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="teacher-info">
                <span class="teacher-badge ${deptClass}">${teacher.position || teacher.department}</span>
                <h3>${teacher.name}</h3>
                <p class="teacher-role">${teacher.position || teacher.department}</p>
                ${teacher.phone ? `<p class="teacher-qualification"><i class="fas fa-phone"></i> ${teacher.phone}</p>` : ''}
                ${teacher.bio ? `<p class="teacher-bio">${teacher.bio}</p>` : ''}
                
                ${teacher.specialties ? `
                <div class="teacher-specialties">
                    ${teacher.specialties.split(',').map(s => `<span class="specialty-tag">${s.trim()}</span>`).join('')}
                </div>` : ''}
                
                <div class="teacher-stats">
                    ${teacher.experience_years ? `
                    <div class="teacher-stat-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>${teacher.experience_years}+ Years Experience</span>
                    </div>` : ''}
                    ${teacher.qualification ? `
                    <div class="teacher-stat-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>${teacher.qualification}</span>
                    </div>` : ''}
                    ${teacher.students_count ? `
                    <div class="teacher-stat-item">
                        <i class="fas fa-users"></i>
                        <span>${teacher.students_count} Students</span>
                    </div>` : ''}
                    ${teacher.subjects_taught ? `
                    <div class="teacher-stat-item">
                        <i class="fas fa-book"></i>
                        <span>${teacher.subjects_taught}</span>
                    </div>` : ''}
                </div>
            </div>
        </div>
        `;
    }).join('');
}

// Filter teachers by department
function filterTeachers(department) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Load teachers
    loadTeachers(department);
}

// Search teachers
function searchTeachers() {
    const searchInput = document.getElementById('teacherSearch');
    if (!searchInput) return;

    const searchTerm = searchInput.value.toLowerCase();
    const teacherCards = document.querySelectorAll('.teacher-card');

    teacherCards.forEach(card => {
        const name = card.querySelector('.teacher-name').textContent.toLowerCase();
        const department = card.querySelector('.teacher-department').textContent.toLowerCase();
        const position = card.querySelector('.teacher-position').textContent.toLowerCase();

        if (name.includes(searchTerm) || department.includes(searchTerm) || position.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Initialize teachers page
if (window.location.pathname.includes('Teachers-redesign.html')) {
    document.addEventListener('DOMContentLoaded', () => {
        loadTeachers();
    });
}
