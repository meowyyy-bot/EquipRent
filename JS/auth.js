// Auth Modal JavaScript

// Modal functionality
function openAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Focus on first input
        setTimeout(() => {
            const firstInput = modal.querySelector('input');
            if (firstInput) {
                firstInput.focus();
            }
        }, 100);
    }
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset forms
        resetForms();
    }
}

// Tab switching
function switchTab(tabName) {
    // Remove active class from all tabs and forms
    const tabBtns = document.querySelectorAll('.tab-btn');
    const formContainers = document.querySelectorAll('.form-container');
    
    tabBtns.forEach(btn => btn.classList.remove('active'));
    formContainers.forEach(form => form.classList.remove('active'));
    
    // Add active class to selected tab and form
    if (tabName === 'login') {
        document.querySelector('.tab-btn:first-child').classList.add('active');
        document.getElementById('loginForm').classList.add('active');
    } else {
        document.querySelector('.tab-btn:last-child').classList.add('active');
        document.getElementById('registerForm').classList.add('active');
    }
}

// Password toggle functionality
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggleBtn = input.nextElementSibling;
    const icon = toggleBtn.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Form handling
function handleLogin(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const username = formData.get('username');
    const password = formData.get('password');
    const remember = formData.get('remember');
    
    // Basic validation
    if (!username || !password) {
        showNotification('Please fill in all fields', 'error');
        return;
    }
    
    // Here you would typically send the data to your backend
    // For demo purposes, let's simulate a successful login
    if (username === 'admin' && password === 'password') {
        showNotification('Login successful!', 'success');
        closeAuthModal();
        
        // Update UI to show logged in state
        updateLoginState(true, username);
    } else {
        showNotification('Invalid username or password', 'error');
    }
}

function handleRegister(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const name = formData.get('name');
    const username = formData.get('username');
    const password = formData.get('password');
    const confirmPassword = formData.get('confirmPassword');
    const terms = formData.get('terms');
    
    // Basic validation
    if (!name || !username || !password || !confirmPassword) {
        showNotification('Please fill in all fields', 'error');
        return;
    }
    
    if (password !== confirmPassword) {
        showNotification('Passwords do not match', 'error');
        return;
    }
    
    if (!terms) {
        showNotification('You must agree to the terms and conditions', 'error');
        return;
    }
    
    if (password.length < 6) {
        showNotification('Password must be at least 6 characters long', 'error');
        return;
    }
    
    // Here you would typically send the data to your backend
    // For demo purposes, let's simulate a successful registration
    showNotification('Account created successfully! You can now login.', 'success');
    
    // Switch to login tab
    switchTab('login');
    
    // Clear register form
    event.target.reset();
}

// Social login handlers
function handleGoogleLogin() {
    showNotification('Google login functionality coming soon!', 'info');
}

function handleFacebookLogin() {
    showNotification('Facebook login functionality coming soon!', 'info');
}

// Update login state
function updateLoginState(isLoggedIn, username = '') {
    const loginBtn = document.querySelector('.login-btn');
    const ctaBtn = document.querySelector('.cta .btn-primary');
    
    if (isLoggedIn) {
        loginBtn.textContent = `Welcome, ${username}`;
        loginBtn.onclick = null;
        loginBtn.style.background = '#10b981';
        
        if (ctaBtn) {
            ctaBtn.textContent = 'Go to Dashboard';
            ctaBtn.onclick = () => showNotification('Dashboard coming soon!', 'info');
        }
    } else {
        loginBtn.textContent = 'Login';
        loginBtn.onclick = openAuthModal;
        loginBtn.style.background = '#2563eb';
        
        if (ctaBtn) {
            ctaBtn.textContent = 'Sign Up Now';
            ctaBtn.onclick = openAuthModal;
        }
    }
}

// Reset forms
function resetForms() {
    const forms = document.querySelectorAll('.auth-form');
    forms.forEach(form => {
        form.reset();
    });
    
    // Reset password fields to password type
    const passwordInputs = document.querySelectorAll('input[type="text"]');
    passwordInputs.forEach(input => {
        if (input.id.includes('Password')) {
            input.type = 'password';
            const icon = input.nextElementSibling.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    });
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('authModal');
    if (event.target === modal) {
        closeAuthModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('authModal');
        if (modal && modal.style.display === 'flex') {
            closeAuthModal();
        }
        const enlistModal = document.getElementById('enlistModal');
        if (enlistModal && enlistModal.style.display === 'flex') {
            closeEnlistModal();
        }
    }
});

// Enlist Modal Functions
function openEnlistModal() {
    const modal = document.getElementById('enlistModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Focus on first input
        setTimeout(() => {
            const firstInput = modal.querySelector('input');
            if (firstInput) {
                firstInput.focus();
            }
        }, 100);
    }
}

function closeEnlistModal() {
    const modal = document.getElementById('enlistModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset form
        const form = modal.querySelector('.enlist-form');
        if (form) {
            form.reset();
        }
        
        // Show table view by default
        hideEnlistForm();
    }
}

// Enlist form functions
function showEnlistForm() {
    const formContainer = document.getElementById('enlistFormContainer');
    const tableContainer = document.querySelector('.enlist-table-container');
    
    if (formContainer && tableContainer) {
        formContainer.style.display = 'block';
        tableContainer.style.display = 'none';
    }
}

function hideEnlistForm() {
    const formContainer = document.getElementById('enlistFormContainer');
    const tableContainer = document.querySelector('.enlist-table-container');
    
    if (formContainer && tableContainer) {
        formContainer.style.display = 'none';
        tableContainer.style.display = 'block';
    }
}

function submitEnlistForm() {
    showEnlistForm();
}

// Close enlist modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('enlistModal');
    if (event.target === modal) {
        closeEnlistModal();
    }
});

// Form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is already logged in (from localStorage or session)
    const savedUsername = localStorage.getItem('username');
    if (savedUsername) {
        updateLoginState(true, savedUsername);
    }
    
    // Initialize enlist modal functionality
    initializeEnlistModal();
});

// Initialize enlist modal functionality
function initializeEnlistModal() {
    const filterCategory = document.getElementById('filterCategory');
    const filterStatus = document.getElementById('filterStatus');
    const searchInput = document.getElementById('searchEquipment');
    const filterBtn = document.querySelector('.btn-filter');
    const clearBtn = document.querySelector('.btn-clear');

    if (filterBtn) {
        filterBtn.addEventListener('click', function() {
            // Implement filter logic here
            console.log('Filtering by:', {
                category: filterCategory ? filterCategory.value : '',
                status: filterStatus ? filterStatus.value : '',
                search: searchInput ? searchInput.value : ''
            });
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (filterCategory) filterCategory.value = '';
            if (filterStatus) filterStatus.value = '';
            if (searchInput) searchInput.value = '';
        });
    }
}

// Notification system (if not already defined in main.js)
if (typeof showNotification === 'undefined') {
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Style the notification
        notification.style.cssText = `
            position: fixed;
            top: 90px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#2563eb'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            max-width: 300px;
            word-wrap: break-word;
            font-size: 0.9rem;
            font-weight: 500;
        `;
        
        // Mobile responsive notification
        if (window.innerWidth <= 768) {
            notification.style.cssText = `
                position: fixed;
                top: 80px;
                left: 20px;
                right: 20px;
                background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#2563eb'};
                color: white;
                padding: 1rem;
                border-radius: 8px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                z-index: 10000;
                transform: translateY(-100px);
                transition: transform 0.3s ease;
                max-width: none;
                word-wrap: break-word;
                font-size: 0.9rem;
                font-weight: 500;
                text-align: center;
            `;
        }
        
        // Add to DOM
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = window.innerWidth <= 768 ? 'translateY(0)' : 'translateX(0)';
        }, 100);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = window.innerWidth <= 768 ? 'translateY(-100px)' : 'translateX(400px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }
}
