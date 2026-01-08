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

    container.innerHTML = teachers.map(teacher => `
        <div class="teacher-card" data-aos="fade-up">
            <div class="teacher-image-wrapper">
                <img src="../${teacher.photo_url || 'img/default-avatar.png'}" 
                     alt="${teacher.name}" 
                     class="teacher-image"
                     onerror="this.src='../img/default-avatar.png'">
                <div class="teacher-overlay">
                    <div class="teacher-social">
                        ${teacher.facebook ? `<a href="${teacher.facebook}" target="_blank"><i class="fab fa-facebook"></i></a>` : ''}
                        ${teacher.twitter ? `<a href="${teacher.twitter}" target="_blank"><i class="fab fa-twitter"></i></a>` : ''}
                        ${teacher.linkedin ? `<a href="${teacher.linkedin}" target="_blank"><i class="fab fa-linkedin"></i></a>` : ''}
                    </div>
                </div>
            </div>
            <div class="teacher-info">
                <h3 class="teacher-name">${teacher.name}</h3>
                <p class="teacher-position">${teacher.position || teacher.department}</p>
                <p class="teacher-department">${teacher.department}</p>
                ${teacher.qualification ? `<p class="teacher-qualification"><i class="fas fa-graduation-cap"></i> ${teacher.qualification}</p>` : ''}
                ${teacher.bio ? `<p class="teacher-bio">${teacher.bio.substring(0, 100)}${teacher.bio.length > 100 ? '...' : ''}</p>` : ''}
                <div class="teacher-stats">
                    ${teacher.experience_years ? `<span><i class="fas fa-clock"></i> ${teacher.experience_years} years</span>` : ''}
                    ${teacher.students_count ? `<span><i class="fas fa-users"></i> ${teacher.students_count} students</span>` : ''}
                </div>
            </div>
        </div>
    `).join('');
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
