<?php
session_start();
// Removed database include since we're just working on frontend
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAMO - Login</title>
    <link rel="stylesheet" href="../CSS/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="login-container">
        <div class="login-image">
            <img src="../Images/STI-LOGO.png" alt="STI Logo" style="max-width: 200px; height: auto;">
        </div>

        <div class="login-form-container">
            <!-- Removed action="auth.php" since we don't have backend yet -->
            <form id="loginForm" class="login-form">
                <h1>Welcome to PAMO</h1>
                <p class="subtitle">STI College Lucena's Inventory System</p>

                <div class="role-selector">
                    <button type="button" class="role-btn active" data-role="student">Student</button>
                    <button type="button" class="role-btn" data-role="officer">Purchasing Officer</button>
                </div>

                <div class="form-group">
                    <label for="email">Email / Student ID</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="email" name="email" placeholder="Enter your email or student ID"
                            required>
                        <span class="validation-message"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <i class="fas fa-eye-slash toggle-password"></i>
                        <span class="validation-message"></span>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                    <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                </div>

                <button type="submit" class="login-btn">
                    <span class="btn-text">Login</span>
                    <div class="spinner"></div>
                </button>

                <div class="login-footer">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- For frontend demo purposes, modify the login.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });

            // Form submission (for frontend demo)
            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();
                loginBtn.classList.add('loading');

                // Simulate login delay
                setTimeout(() => {
                    loginBtn.classList.remove('loading');
                    // For demo purposes, just redirect to index.php
                    window.location.href = 'ProHome.php';
                }, 1500);
            });
        });
    </script>
</body>

</html>