// Field Student Enrollment System - Auth & Login Logic (login.js)

document.addEventListener('DOMContentLoaded', () => {
    const currentPath = window.location.pathname;

    // Session Guard for login.html: if already logged in, redirect to dashboard
    if (sessionStorage.getItem('isLoggedIn') === 'true' && (currentPath.includes('login.html') || currentPath.endsWith('/'))) {
        window.location.href = 'report.php';
        return;
    }

    // Attach handler to login form if present
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
});

// Session Guard for Protected Pages (report.php, add_enrollment.php, enrolled_list.php, profile.php)
function checkAuth() {
    const currentPath = window.location.pathname;
    const isLoginPage = currentPath.includes('login.html') || currentPath.includes('register.php');

    if (!isLoginPage && sessionStorage.getItem('isLoggedIn') !== 'true') {
        window.location.href = 'login.html';
    }
}

// Handle Sign In Authentication
async function handleLogin(e) {
    e.preventDefault();

    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const alertBox = document.getElementById('loginAlert');

    const username = usernameInput ? usernameInput.value.trim() : '';
    const password = passwordInput ? passwordInput.value.trim() : '';

    if (!username || !password) {
        if (alertBox) {
            alertBox.style.display = 'block';
            alertBox.textContent = 'Please enter both username and password.';
        }
        return;
    }

    // 1. Demo Credentials Authentication (admin / admin123)
    if (username === 'admin' && password === 'admin123') {
        sessionStorage.setItem('isLoggedIn', 'true');
        sessionStorage.setItem('loggedInUser', 'Training Officer (Admin)');
        window.location.href = 'report.php';
        return;
    }

    // 2. Database User Authentication via REST API
    try {
        const response = await fetch('api.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
            sessionStorage.setItem('isLoggedIn', 'true');
            sessionStorage.setItem('loggedInUser', result.user?.username || 'Training Officer');
            window.location.href = 'report.php';
        } else {
            if (alertBox) {
                alertBox.style.display = 'block';
                alertBox.textContent = result.message || 'Invalid username or password. Demo login: admin / admin123';
            }
        }
    } catch (error) {
        console.warn('API login call error:', error);
        if (alertBox) {
            alertBox.style.display = 'block';
            alertBox.textContent = 'Authentication error. Please try admin / admin123 for demo mode.';
        }
    }
}

// Sign-Out / Session Destruction Function
function logout() {
    sessionStorage.removeItem('isLoggedIn');
    sessionStorage.removeItem('loggedInUser');
    window.location.href = 'logout.php';
}
