// Roles and Permissions Management

let currentRoleId = null;
let allPermissions = [];

// API Request Helper
async function apiRequest(endpoint, options = {}) {
    try {
        const response = await fetch(`../api${endpoint}`, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { success: false, message: error.message };
    }
}

// Load Roles
async function loadRoles() {
    try {
        const data = await apiRequest('/roles/list.php');
        const rolesGrid = document.getElementById('rolesGrid');
        
        if (data.success && data.data.length > 0) {
            rolesGrid.innerHTML = data.data.map(role => `
                <div class="role-card ${role.is_active ? '' : 'inactive'}" data-level="${role.level}">
                    <div class="role-card-header">
                        <div class="role-icon ${getRoleColorClass(role.role_name)}">
                            <i class="${getRoleIcon(role.role_name)}"></i>
                        </div>
                        <div class="role-badge ${role.is_active ? 'active' : 'inactive'}">
                            ${role.is_active ? 'Active' : 'Inactive'}
                        </div>
                    </div>
                    <div class="role-card-body">
                        <h3>${role.role_display_name}</h3>
                        <p class="role-description">${role.description || 'No description'}</p>
                        <div class="role-stats">
                            <div class="role-stat">
                                <i class="fas fa-users"></i>
                                <span>${role.user_count} Users</span>
                            </div>
                            <div class="role-stat">
                                <i class="fas fa-key"></i>
                                <span>${role.permission_count} Permissions</span>
                            </div>
                            <div class="role-stat">
                                <i class="fas fa-layer-group"></i>
                                <span>Level ${role.level}</span>
                            </div>
                        </div>
                    </div>
                    <div class="role-card-footer">
                        <button class="btn-role-action" onclick="managePermissions(${role.id}, '${role.role_display_name}', '${role.description}')">
                            <i class="fas fa-key"></i> Manage Permissions
                        </button>
                        ${role.role_name !== 'super_admin' ? `
                            <button class="btn-role-action secondary" onclick="editRole(${role.id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        } else {
            rolesGrid.innerHTML = '<p class="no-data">No roles found</p>';
        }
    } catch (error) {
        console.error('Error loading roles:', error);
        document.getElementById('rolesGrid').innerHTML = '<p class="no-data">Failed to load roles</p>';
    }
}

// Get Role Icon
function getRoleIcon(roleName) {
    const icons = {
        'super_admin': 'fas fa-crown',
        'admin': 'fas fa-user-shield',
        'teacher': 'fas fa-chalkboard-teacher',
        'accountant': 'fas fa-calculator',
        'librarian': 'fas fa-book-reader',
        'receptionist': 'fas fa-user-tie',
        'parent': 'fas fa-user-friends',
        'student': 'fas fa-user-graduate'
    };
    return icons[roleName] || 'fas fa-user';
}

// Get Role Color Class
function getRoleColorClass(roleName) {
    const colors = {
        'super_admin': 'gold',
        'admin': 'blue',
        'teacher': 'green',
        'accountant': 'purple',
        'librarian': 'orange',
        'receptionist': 'teal',
        'parent': 'pink',
        'student': 'cyan'
    };
    return colors[roleName] || 'gray';
}

// Manage Permissions
async function managePermissions(roleId, roleName, roleDescription) {
    currentRoleId = roleId;
    
    document.getElementById('modalRoleName').textContent = roleName;
    document.getElementById('modalRoleDescription').textContent = roleDescription;
    
    const modal = document.getElementById('permissionsModal');
    modal.style.display = 'flex';
    
    await loadPermissions(roleId);
}

// Load Permissions
async function loadPermissions(roleId) {
    try {
        const data = await apiRequest(`/permissions/list.php?role_id=${roleId}`);
        const container = document.getElementById('permissionsContainer');
        
        if (data.success && data.grouped) {
            allPermissions = data.data;
            
            container.innerHTML = Object.keys(data.grouped).map(module => `
                <div class="permission-module">
                    <div class="module-header">
                        <h4><i class="fas fa-folder"></i> ${module}</h4>
                        <label class="module-toggle">
                            <input type="checkbox" onchange="toggleModule('${module}', this.checked)">
                            <span>Select All</span>
                        </label>
                    </div>
                    <div class="permissions-list">
                        ${data.grouped[module].map(perm => `
                            <label class="permission-item" data-module="${module}">
                                <input type="checkbox" 
                                       class="permission-checkbox" 
                                       data-permission-id="${perm.id}"
                                       ${perm.is_granted ? 'checked' : ''}>
                                <div class="permission-info">
                                    <span class="permission-name">${perm.permission_display_name}</span>
                                    <span class="permission-desc">${perm.description || ''}</span>
                                </div>
                                <i class="fas fa-check-circle permission-check"></i>
                            </label>
                        `).join('')}
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="no-data">No permissions found</p>';
        }
    } catch (error) {
        console.error('Error loading permissions:', error);
        document.getElementById('permissionsContainer').innerHTML = '<p class="no-data">Failed to load permissions</p>';
    }
}

// Toggle Module Permissions
function toggleModule(module, checked) {
    const checkboxes = document.querySelectorAll(`.permission-item[data-module="${module}"] .permission-checkbox`);
    checkboxes.forEach(cb => cb.checked = checked);
}

// Save Permissions
async function savePermissions() {
    if (!currentRoleId) return;
    
    const checkboxes = document.querySelectorAll('.permission-checkbox:checked');
    const permissionIds = Array.from(checkboxes).map(cb => parseInt(cb.dataset.permissionId));
    
    try {
        const data = await apiRequest('/roles/assign_permissions.php', {
            method: 'POST',
            body: JSON.stringify({
                role_id: currentRoleId,
                permission_ids: permissionIds
            })
        });
        
        if (data.success) {
            alert('Permissions saved successfully!');
            closePermissionsModal();
            loadRoles();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error saving permissions:', error);
        alert('Failed to save permissions');
    }
}

// Close Permissions Modal
function closePermissionsModal() {
    document.getElementById('permissionsModal').style.display = 'none';
    currentRoleId = null;
}

// Toggle Sidebar
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('expanded');
}

// Dropdown Toggle
function toggleDropdown(event, id) {
    event.preventDefault();
    const dropdown = document.getElementById(id + '-dropdown');
    const arrow = event.currentTarget.querySelector('.dropdown-arrow');
    
    dropdown.classList.toggle('active');
    arrow.classList.toggle('rotated');
}

// Logout
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'index.html';
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadRoles();
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('permissionsModal');
    if (event.target === modal) {
        closePermissionsModal();
    }
}
