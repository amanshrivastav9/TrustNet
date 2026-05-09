<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        $name = sanitizeInput($_POST['name']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            $error = 'All fields are required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match';
        } else {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered';
            } else {
                // Generate OTP
                $otp_code = sprintf("%06d", mt_rand(1, 999999));
                $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
                
                // Insert user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, otp_code, otp_expires) 
                                       VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$name, $email, $hashed_password, $otp_code, $otp_expires])) {
                    $user_id = $pdo->lastInsertId();
                    
                    // Send OTP email
                    require_once 'includes/mailer.php';
                    $email_sent = sendOTPEmail($email, $name, $otp_code);
                    
                    if ($email_sent) {
                        $_SESSION['temp_user_id'] = $user_id;
                        $_SESSION['temp_email'] = $email;
                        $_SESSION['temp_name'] = $name;
                        header('Location: verify-otp.php');
                        exit();
                    } else {
                        // Delete user if email failed
                        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        $error = 'Failed to send OTP email. Please check your email address or try again later.';
                    }
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TrustNet Security</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        .strength-weak { color: #ff4757; }
        .strength-medium { color: #ffa502; }
        .strength-strong { color: #00D1FF; }
        .requirements {
            font-size: 12px;
            color: #7B61FF;
            margin-top: 5px;
        }
        .requirements ul {
            margin: 5px 0 0 20px;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #7B61FF;
        }
        .password-container {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <div class="logo">
                <h1>🔒 TrustNet Security</h1>
                <p>Create your account to get started</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <strong>Success!</strong> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="registerForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required class="form-control" 
                           placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required class="form-control" 
                           placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required class="form-control" 
                               placeholder="Minimum 8 characters">
                        <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                    <div class="requirements">
                        Password must contain:
                        <ul>
                            <li id="req-length">At least 8 characters</li>
                            <li id="req-upper">At least 1 uppercase letter</li>
                            <li id="req-lower">At least 1 lowercase letter</li>
                            <li id="req-number">At least 1 number</li>
                            <li id="req-special">At least 1 special character</li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-container">
                        <input type="password" id="confirm_password" name="confirm_password" required class="form-control" 
                               placeholder="Confirm your password">
                        <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="terms" required>
                        I agree to the <a href="#" style="color: #00D1FF;">Terms of Service</a> and 
                        <a href="#" style="color: #00D1FF;">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" id="registerBtn">Register</button>
                
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
            field.setAttribute('type', type);
        }
        
        // Password strength checker
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        
        function checkPasswordStrength(password) {
            let strength = 0;
            let requirements = {
                length: password.length >= 8,
                upper: /[A-Z]/.test(password),
                lower: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };
            
            // Update requirement indicators
            document.getElementById('req-length').style.color = requirements.length ? '#00D1FF' : '#ff4757';
            document.getElementById('req-upper').style.color = requirements.upper ? '#00D1FF' : '#ff4757';
            document.getElementById('req-lower').style.color = requirements.lower ? '#00D1FF' : '#ff4757';
            document.getElementById('req-number').style.color = requirements.number ? '#00D1FF' : '#ff4757';
            document.getElementById('req-special').style.color = requirements.special ? '#00D1FF' : '#ff4757';
            
            // Calculate strength
            if (requirements.length) strength++;
            if (requirements.upper) strength++;
            if (requirements.lower) strength++;
            if (requirements.number) strength++;
            if (requirements.special) strength++;
            
            // Display strength message
            const strengthDiv = document.getElementById('passwordStrength');
            if (password.length === 0) {
                strengthDiv.innerHTML = '';
                strengthDiv.className = 'password-strength';
            } else if (strength <= 2) {
                strengthDiv.innerHTML = 'Weak password';
                strengthDiv.className = 'password-strength strength-weak';
            } else if (strength <= 4) {
                strengthDiv.innerHTML = 'Medium password';
                strengthDiv.className = 'password-strength strength-medium';
            } else {
                strengthDiv.innerHTML = 'Strong password!';
                strengthDiv.className = 'password-strength strength-strong';
            }
            
            return strength >= 4;
        }
        
        // Check password match
        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            
            if (confirm.length > 0) {
                if (password !== confirm) {
                    confirmInput.style.borderColor = '#ff4757';
                    return false;
                } else {
                    confirmInput.style.borderColor = '#00D1FF';
                    return true;
                }
            }
            return true;
        }
        
        // Real-time validation
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });
        
        confirmInput.addEventListener('input', checkPasswordMatch);
        
        // Form validation before submit
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            const terms = document.getElementById('terms').checked;
            
            if (!checkPasswordStrength(password)) {
                e.preventDefault();
                alert('Please ensure your password meets all requirements');
                return false;
            }
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match');
                return false;
            }
            
            if (!terms) {
                e.preventDefault();
                alert('Please accept the Terms of Service and Privacy Policy');
                return false;
            }
            
            // Show loading state
            const btn = document.getElementById('registerBtn');
            btn.innerHTML = 'Registering...';
            btn.disabled = true;
            
            return true;
        });
        
        // Email validation
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailPattern.test(email)) {
                this.style.borderColor = '#ff4757';
                showNotification('Please enter a valid email address', 'error');
            } else {
                this.style.borderColor = '#00D1FF';
            }
        });
        
        // Show notification function
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type}`;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            notification.innerHTML = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
</body>
</html>