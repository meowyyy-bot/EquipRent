// Authentication Modal Function
function initializeAuthModal() {
    // DOM Elements
    const authModal = document.getElementById('authModal');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const tabBtns = document.querySelectorAll('.tab-btn');
    
    // Modal Functions
    function openAuthModal() {
        authModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        // Default to login tab
        switchTab('login');
    }
    
    function closeAuthModal() {
        authModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        // Reset forms
        resetForms();
    }
    
    function switchTab(tabName) {
        // Remove active class from all tabs and forms
        tabBtns.forEach(btn => btn.classList.remove('active'));
        loginForm.classList.remove('active');
        registerForm.classList.remove('active');
        
        // Add active class to selected tab and form
        if (tabName === 'login') {
            document.querySelector('[onclick="switchTab(\'login\')"]').classList.add('active');
            loginForm.classList.add('active');
        } else {
            document.querySelector('[onclick="switchTab(\'register\')"]').classList.add('active');
            registerForm.classList.add('active');
        }
    }
    
    function resetForms() {
        // Reset login form
        document.getElementById('loginEmail').value = '';
        document.getElementById('loginPassword').value = '';
        document.querySelector('input[name="remember"]').checked = false;
        
        // Reset register form
        document.getElementById('registerName').value = '';
        document.getElementById('registerEmail').value = '';
        document.getElementById('registerPassword').value = '';
        document.getElementById('confirmPassword').value = '';
        document.querySelector('input[name="terms"]').checked = false;
        
        // Clear any error messages
        clearErrorMessages();
    }
    
    function clearErrorMessages() {
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(msg => msg.remove());
    }
    
    function showError(inputElement, message) {
        clearErrorMessages();
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#e74c3c';
        errorDiv.style.fontSize = '0.8rem';
        errorDiv.style.marginTop = '0.3rem';
        errorDiv.textContent = message;
        
        inputElement.parentNode.appendChild(errorDiv);
        inputElement.style.borderColor = '#e74c3c';
    }
    
    function clearError(inputElement) {
        const errorMessage = inputElement.parentNode.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
        inputElement.style.borderColor = '#e1e5e9';
    }
    
    // Form Validation
    function validateLoginForm() {
        const email = document.getElementById('loginEmail');
        const password = document.getElementById('loginPassword');
        let isValid = true;
        
        // Clear previous errors
        clearError(email);
        clearError(password);
        
        // Email validation
        if (!email.value.trim()) {
            showError(email, 'Email is required');
            isValid = false;
        } else if (!isValidEmail(email.value)) {
            showError(email, 'Please enter a valid email');
            isValid = false;
        }
        
        // Password validation
        if (!password.value.trim()) {
            showError(password, 'Password is required');
            isValid = false;
        } else if (password.value.length < 6) {
            showError(password, 'Password must be at least 6 characters');
            isValid = false;
        }
        
        return isValid;
    }
    
    function validateRegisterForm() {
        const name = document.getElementById('registerName');
        const email = document.getElementById('registerEmail');
        const password = document.getElementById('registerPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const terms = document.querySelector('input[name="terms"]');
        let isValid = true;
        
        // Clear previous errors
        clearError(name);
        clearError(email);
        clearError(password);
        clearError(confirmPassword);
        
        // Name validation
        if (!name.value.trim()) {
            showError(name, 'Full name is required');
            isValid = false;
        } else if (name.value.trim().length < 2) {
            showError(name, 'Name must be at least 2 characters');
            isValid = false;
        }
        
        // Email validation
        if (!email.value.trim()) {
            showError(email, 'Email is required');
            isValid = false;
        } else if (!isValidEmail(email.value)) {
            showError(email, 'Please enter a valid email');
            isValid = false;
        }
        
        // Password validation
        if (!password.value.trim()) {
            showError(password, 'Password is required');
            isValid = false;
        } else if (password.value.length < 6) {
            showError(password, 'Password must be at least 6 characters');
            isValid = false;
        }
        
        // Confirm password validation
        if (!confirmPassword.value.trim()) {
            showError(confirmPassword, 'Please confirm your password');
            isValid = false;
        } else if (password.value !== confirmPassword.value) {
            showError(confirmPassword, 'Passwords do not match');
            isValid = false;
        }
        
        // Terms validation
        if (!terms.checked) {
            const termsLabel = terms.parentNode;
            if (!termsLabel.querySelector('.error-message')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.style.color = '#e74c3c';
                errorDiv.style.fontSize = '0.8rem';
                errorDiv.style.marginTop = '0.3rem';
                errorDiv.textContent = 'You must agree to the terms and conditions';
                termsLabel.appendChild(errorDiv);
            }
            isValid = false;
        }
        
        return isValid;
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Form Handlers
    function handleLogin(event) {
        event.preventDefault();
        
        if (!validateLoginForm()) {
            return;
        }
        
        const formData = {
            email: document.getElementById('loginEmail').value,
            password: document.getElementById('loginPassword').value,
            remember: document.querySelector('input[name="remember"]').checked
        };
        
        // Show loading state
        const submitBtn = event.target.querySelector('.submit-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
        submitBtn.disabled = true;
        
        // Simulate API call (replace with actual authentication logic)
        setTimeout(() => {
            console.log('Login attempt:', formData);
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Show success message (replace with actual redirect logic)
            showSuccessMessage('Login successful! Redirecting...');
            
            // Close modal after delay
            setTimeout(() => {
                closeAuthModal();
                // Redirect to dashboard or user area
                // window.location.href = '/dashboard';
            }, 1500);
            
        }, 1500);
    }
    
    function handleRegister(event) {
        event.preventDefault();
        
        if (!validateRegisterForm()) {
            return;
        }
        
        const formData = {
            name: document.getElementById('registerName').value,
            email: document.getElementById('registerEmail').value,
            password: document.getElementById('registerPassword').value,
            terms: document.querySelector('input[name="terms"]').checked
        };
        
        // Show loading state
        const submitBtn = event.target.querySelector('.submit-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
        submitBtn.disabled = true;
        
        // Simulate API call (replace with actual registration logic)
        setTimeout(() => {
            console.log('Registration attempt:', formData);
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Show success message
            showSuccessMessage('Account created successfully! Please check your email for verification.');
            
            // Close modal after delay
            setTimeout(() => {
                closeAuthModal();
            }, 2000);
            
        }, 1500);
    }
    
    function showSuccessMessage(message) {
        // Remove existing success messages
        const existingMessages = document.querySelectorAll('.success-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Create success message
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.style.backgroundColor = '#d4edda';
        successDiv.style.color = '#155724';
        successDiv.style.padding = '1rem';
        successDiv.style.borderRadius = '8px';
        successDiv.style.marginTop = '1rem';
        successDiv.style.textAlign = 'center';
        successDiv.style.border = '1px solid #c3e6cb';
        successDiv.textContent = message;
        
        // Insert after the form
        const activeForm = document.querySelector('.form-container.active form');
        activeForm.parentNode.insertBefore(successDiv, activeForm.nextSibling);
    }
    
    // Password Toggle Function
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.parentNode.querySelector('.toggle-password i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
    
    // Social Login Handlers
    function handleGoogleLogin() {
        console.log('Google login clicked');
        // Implement Google OAuth logic here
        showSuccessMessage('Google login functionality coming soon!');
    }
    
    function handleFacebookLogin() {
        console.log('Facebook login clicked');
        // Implement Facebook OAuth logic here
        showSuccessMessage('Facebook login functionality coming soon!');
    }
    
    // Event Listeners
    function setupEventListeners() {
        // Close modal when clicking outside
        authModal.addEventListener('click', (e) => {
            if (e.target === authModal) {
                closeAuthModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && authModal.style.display === 'block') {
                closeAuthModal();
            }
        });
        
        // Real-time validation
        const inputs = document.querySelectorAll('.auth-form input');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                if (input.type !== 'checkbox') {
                    clearError(input);
                }
            });
        });
        
        // Social login buttons
        const googleBtns = document.querySelectorAll('.social-btn.google');
        const facebookBtns = document.querySelectorAll('.social-btn.facebook');
        
        googleBtns.forEach(btn => {
            btn.addEventListener('click', handleGoogleLogin);
        });
        
        facebookBtns.forEach(btn => {
            btn.addEventListener('click', handleFacebookLogin);
        });
    }
    
    // Initialize the modal
    function init() {
        setupEventListeners();
        console.log('Auth modal initialized successfully!');
    }
    
    // Return public functions
    return {
        init,
        openAuthModal,
        closeAuthModal,
        switchTab,
        handleLogin,
        handleRegister,
        togglePassword
    };
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const authModal = initializeAuthModal();
    
    // Make functions globally available for HTML onclick attributes
    window.openAuthModal = authModal.openAuthModal;
    window.closeAuthModal = authModal.closeAuthModal;
    window.switchTab = authModal.switchTab;
    window.handleLogin = authModal.handleLogin;
    window.handleRegister = authModal.handleRegister;
    window.togglePassword = authModal.togglePassword;
    
    // Initialize the modal
    authModal.init();
});

function openLoginModal() {
    // Show your login modal here
    document.getElementById('loginModal').style.display = 'block';
}
