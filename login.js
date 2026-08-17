document.addEventListener('DOMContentLoaded', () => {
    // Redirect to dashboard if session active
    if (sessionStorage.getItem('isLoggedIn') === 'true' && window.location.pathname.includes('login.html')) {
        window.location.href = 'home.php';
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
});

function handleLogin(e) {
    e.preventDefault();

    const usernameInput = document.getElementById('username').value.trim();
    const passwordInput = document.getElementById('password').value.trim();
    const alertBox = document.getElementById('loginAlert');

    // Default Demo Credentials
    const validUsername = 'admin';
    const validPassword = 'admin123';

    if (usernameInput === validUsername && passwordInput === validPassword) {
        sessionStorage.setItem('isLoggedIn', 'true');
        sessionStorage.setItem('loggedInUser', 'Training Officer');
        window.location.href = 'home.php';
    } else {
        alertBox.style.display = 'block';
        alertBox.textContent = 'Invalid username or password. Try admin / admin123';
    }
}

// Session Guard for Protected Pages
function checkAuth() {
    if (sessionStorage.getItem('isLoggedIn') !== 'true') {
        window.location.href = 'login.html';
    }
}

// Logout Functionality
function logout() {
    sessionStorage.removeItem('isLoggedIn');
    sessionStorage.removeItem('loggedInUser');
    window.location.href = 'login.html';
}