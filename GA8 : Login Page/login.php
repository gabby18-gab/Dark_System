<?php
session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: client/index.php');
    exit();
}

$error = '';
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
    $pdo = new PDO("mysql:host=localhost;dbname=fabulous_finds", "root", "");
    
    $stmt = $pdo->prepare("SELECT UserID, Name, Password, Email FROM user WHERE Email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
        if ($user && password_verify($password, $user['Password'])) {
            // Check if user is admin
            if ($user['Email'] == 'admin@fabulousfinds.com') {
                $error = 'Admin must login through the admin portal.';
            } else {
                // Login successful for regular customers
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['user_name'] = $user['Name'];
                $_SESSION['logged_in'] = true;
            
                $success = true;
            }
        } else {
            $error = 'Invalid email or password!';
        }
    } catch (Exception $e) {
    $error = 'Login failed. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>FABULOUS FINDS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/jpg" href="assets/img/Fabulous-finds.png">
    <link rel="stylesheet" href="assets/css/login-style.css">
    <style>
        .success-message {
            display: none;
        }
        .success-message.show {
            display: block;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <img src="assets/img/Fabulous-finds.png" alt="Fabulous Finds Logo" width="100" height="100">
                </div>
                <h1>Sign in to Fabulous Finds</h1>
                <p>Welcome back! Please sign in to continue.</p>
            </div>

            <?php if ($success): ?>

                <div class="success-message show" id="successMessage">
                    <div class="success-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="12" fill="#635BFF"/>
                            <path d="M8 12l3 3 5-5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>Welcome back!</h3>
                    <p>Redirecting to your main page...</p>
                </div>

                <script>
                    setTimeout(function() {
                        window.location.href = 'client/index.php';
                    }, 2000);
                </script>
                
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger" style="color: #f56565; text-align: center; margin-bottom: 15px; padding: 10px; background: #fef5f5; border-radius: 5px; border: 1px solid #f56565;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>


                <form class="login-form" id="loginForm" method="POST" action="" novalidate>
                    <div class="input-group">
                        <input type="email" id="email" name="email" required autocomplete="email" placeholder=" ">
                        <label for="email">Email address</label>
                        <span class="input-border"></span>
                        <span class="error-message" id="emailError"></span>
                    </div>

                    <div class="input-group">
                        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder=" ">
                        <label for="password">Password</label>
                        <span class="input-border"></span>
                        <span class="error-message" id="passwordError"></span>
                    </div>

                    <div class="form-options">
                        <label for="checkbox-container">
                            <input type="checkbox" id="remember" name="remember">
                            remember me
                        </label>
                        <a href="#" class="forgot-link"> Forgot password? </a>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">Sign in</span>
                    </button>
                </form>

                <div class="divider">
                    <span>Or continue with</span>
                </div>

                <div class="social-buttons">
                    <button type="button" class="social-btn">
                        <img src="assets/img/google-icon.png" alt="Google" width="16" height="16">
                        Google
                    </button>
                        
                    <button type="button" class="social-btn">
                        <img src="assets/img/facebook-icon.png" alt="Facebook" width="16" height="16">
                        Facebook
                    </button>
                </div>

                <div class="signup-link">
                    <span>Don't have an account?</span>
                    <a href="register.php">Sign up</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            if (form) {
                const submitBtn = document.querySelector('.submit-btn');
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');

                // Floating labels
                const inputs = document.querySelectorAll('.input-group input');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        this.parentElement.classList.add('focused');
                    });
                    
                    input.addEventListener('blur', function() {
                        this.parentElement.classList.remove('focused');
                        if (this.value) {
                            this.parentElement.classList.add('has-value');
                        }
                    });

                    // Check on page load
                    if (input.value) {
                        input.parentElement.classList.add('has-value');
                    }
                });

                // Form submission with loading state
                form.addEventListener('submit', function(e) {
                    // Basic validation
                    let isValid = true;

                    // Email validation
                    if (!emailInput.value || !emailInput.value.includes('@')) {
                        showError(emailInput, 'Please enter a valid email');
                        isValid = false;
                    }

                    // Password validation
                    if (!passwordInput.value) {
                        showError(passwordInput, 'Password is required');
                        isValid = false;
                    }

                    if (!isValid) {
                        e.preventDefault();
                        return;
                    }

                    // Show loading animation
                    if (submitBtn) {
                        submitBtn.classList.add('loading');
                    }
                });

                function showError(input, message) {
                    const inputGroup = input.closest('.input-group');
                    const errorElement = inputGroup.querySelector('.error-message');
                    
                    inputGroup.classList.add('error');
                    errorElement.textContent = message;
                    errorElement.classList.add('show');
                }

                // Clear errors on input
                emailInput.addEventListener('input', () => clearError(emailInput));
                passwordInput.addEventListener('input', () => clearError(passwordInput));

                function clearError(input) {
                    const inputGroup = input.closest('.input-group');
                    const errorElement = inputGroup.querySelector('.error-message');
                    
                    inputGroup.classList.remove('error');
                    errorElement.classList.remove('show');
                }

                // Social buttons
                document.querySelectorAll('.social-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        alert('Social login would be implemented here');
                    });
                });
            }

            console.log('✅ Login form JavaScript loaded!');
        });
    </script>

</body>
</html>