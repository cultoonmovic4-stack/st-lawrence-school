// Load teachers
async function loadTeachers() {
    const department = document.getElementById('departmentFilter').value;
    const search = document.getElementById('searchInput').value;
    
    let url = '/teachers/list.php?';
    if (department) url += `department=${department}&`;
    if (search) url += `search=${search}`;
    
    const data = await apiRequest(url);
    
    const teachersList = document.getElementById('teachersList');
    
    if (data && data.success && data.data.length > 0) {
        teachersList.innerHTML = data.data.map(teacher => `
            <div class="data-item">
                <div class="item-info">
                    <img src="../${teacher.photo_url || 'img/default-avatar.png'}" alt="${teacher.name}" class="item-image">
                    <div>
                        <h3>${teacher.name}</h3>
                        <p>${teacher.department} - ${teacher.position || 'Teacher'}</p>
                        <p class="text-muted">${teacher.email}</p>
                    </div>
                </div>
                <div class="item-actions">
                    <button class="btn-edit" onclick='editTeacher(${JSON.stringify(teacher)})'>Edit</button>
                    <button class="btn-delete" onclick="deleteTeacher(${teacher.id}, '${teacher.name}')">Delete</button>
                </div>
            </div>
        `).join('');
    } else {
        teachersList.innerHTML = '<p class="no-data">No teachers found</p>';
    }
}

// Show add modal
function showAddModal() {
    document.getElementById('modalTitle').textContent = 'Add Teacher';
    document.getElementById('teacherForm').reset();
    document.getElementById('teacherId').value = '';
    document.getElementById('teacherModal').style.display = 'flex';
}

// Edit teacher
function editTeacher(teacher) {
    document.getElementById('modalTitle').textContent = 'Edit Teacher';
    document.getElementById('teacherId').value = teacher.id;
    document.getElementById('name').value = teacher.name;
    document.getElementById('email').value = teacher.email;
    document.getElementById('phone').value = teacher.phone || '';
    document.getElementById('department').value = teacher.department;
    document.getElementById('position').value = teacher.position || '';
    document.getElementById('qualification').value = teacher.qualification || '';
    document.getElementById('bio').value = teacher.bio || '';
    document.getElementById('teacherModal').style.display = 'flex';
}

// Close modal
function closeModal() {
    document.getElementById('teacherModal').style.display = 'none';
}

// Save teacher
document.getElementById('teacherForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const teacherId = document.getElementById('teacherId').value;
    const teacherData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        department: document.getElementById('department').value,
        position: document.getElementById('position').value,
        qualification: document.getElementById('qualification').value,
        bio: document.getElementById('bio').value
    };
    
    let endpoint = '/teachers/create.php';
    let method = 'POST';
    
    if (teacherId) {
        endpoint = '/teachers/update.php';
        method = 'PUT';
        teacherData.id = teacherId;
    }
    
    const result = await apiRequest(endpoint, {
        method: method,
        body: JSON.stringify(teacherData)
    });
    
    if (result && result.success) {
        // Upload photo if selected
        const photoFile = document.getElementById('photo').files[0];
        if (photoFile) {
            const formData = new FormData();
            formData.append('photo', photoFile);
            formData.append('teacher_id', teacherId || result.data.id);
            
            const auth = checkAuth();
            await fetch(`${API_URL}/teachers/upload_photo.php`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${auth.token}`
                },
                body: formData
            });
        }
        
        showSuccessAlert('Success!', 'Teacher saved successfully!', () => {
            closeModal();
            loadTeachers();
        });
    } else {
        showErrorAlert('Error', result ? result.message : 'Failed to save teacher');
    }
});

// Delete teacher
async function deleteTeacher(id, name) {
    showConfirmAlert(
        'Delete Teacher?',
        `Are you sure you want to delete ${name}? This action cannot be undone.`,
        async () => {
            await performDeleteTeacher(id, name);
        }
    );
}

async function performDeleteTeacher(id, name) {
    
    const result = await apiRequest(`/teachers/delete.php?id=${id}`, {
        method: 'DELETE'
    });
    
    if (result && result.success) {
        showSuccessAlert('Deleted!', 'Teacher deleted successfully!', () => {
            loadTeachers();
        });
    } else {
        showErrorAlert('Error', result ? result.message : 'Failed to delete teacher');
    }
}

// Initialize
loadTeachers();
