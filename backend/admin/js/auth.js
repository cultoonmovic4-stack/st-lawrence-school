// API Base URL - Updated for backend folder
const API_URL = '../api';

// Check if user is logged in
function checkAuth() {
    const token = localStorage.getItem('auth_token');
    const user = localStorage.getItem('user');
    
    if (!token || !user) {
        if (!window.location.pathname.includes('index.html')) {
            window.location.href = 'index.html';
        }
        return null;
    }
    
    return {
        token: token,
        user: JSON.parse(user)
    };
}

// Logout function
function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    window.location.href = 'index.html';
}

// Login form handler
if (document.getElementById('loginForm')) {
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const loginBtn = document.getElementById('loginBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');
        const errorMsg = document.getElementById('errorMsg');
        
        // Show loading
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline-block';
        loginBtn.disabled = true;
        errorMsg.style.display = 'none';
        
        try {
            const response = await fetch(`${API_URL}/auth/login.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Save token and user info
                localStorage.setItem('auth_token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                
                // Redirect to dashboard
                window.location.href = 'dashboard.html';
            } else {
                // Show error
                errorMsg.textContent = data.message;
                errorMsg.style.display = 'block';
            }
        } catch (error) {
            errorMsg.textContent = 'Connection error. Please check if XAMPP is running.';
            errorMsg.style.display = 'block';
        } finally {
            btnText.style.display = 'inline';
            btnLoader.style.display = 'none';
            loginBtn.disabled = false;
        }
    });
}

// Check auth on protected pages
if (!window.location.pathname.includes('index.html')) {
    const auth = checkAuth();
    if (auth && document.getElementById('userName')) {
        document.getElementById('userName').textContent = auth.user.username;
    }
}

// API Helper function
async function apiRequest(endpoint, options = {}) {
    const auth = checkAuth();
    if (!auth) return null;
    
    const defaultOptions = {
        headers: {
            'Authorization': `Bearer ${auth.token}`,
            'Content-Type': 'application/json'
        }
    };
    
    const mergedOptions = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...options.headers
        }
    };
    
    try {
        const response = await fetch(`${API_URL}${endpoint}`, mergedOptions);
        const data = await response.json();
        
        if (response.status === 401) {
            logout();
            return null;
        }
        
        return data;
    } catch (error) {
        console.error('API Error:', error);
        return null;
    }
}
