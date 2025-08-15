// Auth Modal JavaScript

// Global variables for focus management
let modalFocusableElements = [];
let firstFocusableElement = null;
let lastFocusableElement = null;

// Modal functionality
function openAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.classList.add('modal-open');
        
        // Prevent background scrolling
        preventBackgroundScroll();
        
        // Set up focus management
        setupModalFocus(modal);
        
        // Focus on first input
        setTimeout(() => {
            if (firstFocusableElement) {
                firstFocusableElement.focus();
            }
        }, 100);
        
        // Add event listeners for focus trapping
        addModalEventListeners(modal);
    }
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
        
        // Restore background scrolling
        restoreBackgroundScroll();
        
        // Remove event listeners
        removeModalEventListeners(modal);
        
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
    
    // Re-setup focus management for the new form
    const modal = document.getElementById('authModal');
    if (modal) {
        setupModalFocus(modal);
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

// Enhanced modal focus management
function setupModalFocus(modal) {
    // Get all focusable elements within the modal
    modalFocusableElements = modal.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    
    if (modalFocusableElements.length > 0) {
        firstFocusableElement = modalFocusableElements[0];
        lastFocusableElement = modalFocusableElements[modalFocusableElements.length - 1];
    }
    
    // Store the element that had focus before modal opened
    modal.dataset.previousFocus = document.activeElement ? document.activeElement.id || 'body' : 'body';
}

function addModalEventListeners(modal) {
    // Focus trap with Tab key
    modal.addEventListener('keydown', handleModalKeydown);
    
    // Prevent clicks outside modal from closing it (unless clicking on backdrop)
    modal.addEventListener('click', handleModalClick);
    
    // Handle window resize to ensure modal stays properly positioned
    window.addEventListener('resize', () => handleModalResize(modal));
}

function removeModalEventListeners(modal) {
    modal.removeEventListener('keydown', handleModalKeydown);
    modal.removeEventListener('click', handleModalClick);
    window.removeEventListener('resize', () => handleModalResize(modal));
}

function handleModalKeydown(event) {
    if (event.key === 'Tab') {
        if (event.shiftKey) {
            // Shift + Tab: go to previous element
            if (document.activeElement === firstFocusableElement) {
                event.preventDefault();
                lastFocusableElement.focus();
            }
        } else {
            // Tab: go to next element
            if (document.activeElement === lastFocusableElement) {
                event.preventDefault();
                firstFocusableElement.focus();
            }
        }
    } else if (event.key === 'Escape') {
        // Close modal on Escape
        const modal = document.getElementById('authModal');
        if (modal && modal.style.display === 'flex') {
            closeAuthModal();
        }
    }
}

function handleModalClick(event) {
    // Only close if clicking on the modal backdrop (not on modal content)
    if (event.target === event.currentTarget) {
        closeAuthModal();
    }
}

function handleModalResize(modal) {
    // Ensure modal stays centered and properly sized on window resize
    if (modal.style.display === 'flex') {
        // Force a reflow to ensure proper positioning
        modal.style.display = 'none';
        modal.offsetHeight; // Trigger reflow
        modal.style.display = 'flex';
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('authModal');
    if (event.target === modal) {
        closeAuthModal();
    }
});

// Form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is already logged in (from localStorage or session)
    const savedUsername = localStorage.getItem('username');
    if (savedUsername) {
        updateLoginState(true, savedUsername);
    }
});

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
