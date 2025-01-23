document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.querySelector('.toggle-password');
    const roleBtns = document.querySelectorAll('.role-btn');
    const loginBtn = document.querySelector('.login-btn');

    // Role selector
    roleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            roleBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Update placeholder based on role
            if (btn.dataset.role === 'student') {
                emailInput.placeholder = 'Enter your student ID';
            } else {
                emailInput.placeholder = 'Enter your email';
            }
        });
    });

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    // Real-time validation
    emailInput.addEventListener('input', validateEmail);
    passwordInput.addEventListener('input', validatePassword);

    function validateEmail() {
        const value = emailInput.value;
        const message = emailInput.nextElementSibling;
        const activeRole = document.querySelector('.role-btn.active').dataset.role;

        if (activeRole === 'student') {
            // Student ID validation
            if (!/^\d{10}$/.test(value)) {
                message.textContent = 'Please enter a valid 10-digit student ID';
                return false;
            }
        } else {
            // Email validation
            if (!/\S+@\S+\.\S+/.test(value)) {
                message.textContent = 'Please enter a valid email address';
                return false;
            }
        }

        message.textContent = '';
        return true;
    }

    function validatePassword() {
        const value = passwordInput.value;
        const message = passwordInput.nextElementSibling.nextElementSibling;

        if (value.length < 8) {
            message.textContent = 'Password must be at least 8 characters long';
            return false;
        }

        if (!/[A-Z]/.test(value)) {
            message.textContent = 'Password must contain at least one uppercase letter';
            return false;
        }

        if (!/[a-z]/.test(value)) {
            message.textContent = 'Password must contain at least one lowercase letter';
            return false;
        }

        if (!/[0-9]/.test(value)) {
            message.textContent = 'Password must contain at least one number';
            return false;
        }

        message.textContent = '';
        return true;
    }

    // Form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateEmail() || !validatePassword()) {
            return;
        }

        // Show loading state
        loginBtn.classList.add('loading');

        // Get form data including redirect URL
        const formData = new FormData(this);

        // Make the actual API call to your backend
        fetch('auth.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to the specified page
                window.location.href = formData.get('redirect') || 'index.php';
            } else {
                // Show error message
                showError(data.message || 'Login failed. Please try again.');
            }
        })
        .catch(error => {
            showError('An error occurred. Please try again.');
        })
        .finally(() => {
            loginBtn.classList.remove('loading');
        });
    });

    function showError(message) {
        // Add this function to show error messages
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        
        const form = document.querySelector('.login-form');
        form.insertBefore(errorDiv, form.firstChild);
        
        // Remove the error message after 3 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 3000);
    }

    // Add floating label effect
    document.querySelectorAll('.input-group input').forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', () => {
            if (!input.value) {
                input.parentElement.classList.remove('focused');
            }
        });
    });
}); 