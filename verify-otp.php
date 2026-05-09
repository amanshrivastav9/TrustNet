<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user came from registration
if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_email'])) {
    header('Location: register.php');
    exit();
}

$error = '';
$success = '';
$resend_message = '';

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verify'])) {
        $otp_code = sanitizeInput($_POST['otp_code']);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND otp_code = ? AND otp_expires > NOW()");
        $stmt->execute([$_SESSION['temp_user_id'], $otp_code]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Verify email
            $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, otp_code = NULL, otp_expires = NULL WHERE id = ?");
            $stmt->execute([$_SESSION['temp_user_id']]);
            
            // Clear temp session
            $user_name = $_SESSION['temp_name'];
            unset($_SESSION['temp_user_id']);
            unset($_SESSION['temp_email']);
            unset($_SESSION['temp_name']);
            
            // Auto login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid or expired OTP code. Please try again.';
        }
    } elseif (isset($_POST['resend'])) {
        // Resend OTP
        $stmt = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['temp_user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate new OTP
            $new_otp = sprintf("%06d", mt_rand(1, 999999));
            $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            $stmt = $pdo->prepare("UPDATE users SET otp_code = ?, otp_expires = ? WHERE id = ?");
            $stmt->execute([$new_otp, $otp_expires, $_SESSION['temp_user_id']]);
            
            // Send email
            require_once 'includes/mailer.php';
            if (sendOTPEmail($user['email'], $user['name'], $new_otp)) {
                $resend_message = 'A new OTP has been sent to your email.';
            } else {
                $error = 'Failed to resend OTP. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - TrustNet Security</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .otp-container {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 30px 0;
        }
        .otp-digit {
            width: 60px;
            height: 70px;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: #00D1FF;
            transition: all 0.3s ease;
        }
        .otp-digit:focus {
            outline: none;
            border-color: #00D1FF;
            background: rgba(0, 209, 255, 0.1);
            box-shadow: 0 0 10px rgba(0, 209, 255, 0.3);
        }
        .otp-digit::-webkit-inner-spin-button,
        .otp-digit::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .timer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #7B61FF;
        }
        .resend-btn {
            background: none;
            border: none;
            color: #00D1FF;
            cursor: pointer;
            text-decoration: underline;
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 14px;
        }
        .resend-btn:disabled {
            color: #666;
            cursor: not-allowed;
            text-decoration: none;
        }
        .verify-btn {
            margin-top: 20px;
        }
        .error-message {
            background: rgba(255, 71, 87, 0.2);
            border: 1px solid #ff4757;
            color: #ff4757;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success-message {
            background: rgba(0, 209, 255, 0.2);
            border: 1px solid #00D1FF;
            color: #00D1FF;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #00D1FF;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-left: 8px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <div class="logo">
                <h1>📧 Email Verification</h1>
                <p>Enter the 6-digit OTP sent to <strong><?php echo htmlspecialchars($_SESSION['temp_email']); ?></strong></p>
            </div>
            
            <div id="errorMessage" class="error-message" style="display: none;"></div>
            <div id="successMessage" class="success-message" style="display: none;"></div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($resend_message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($resend_message); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="otpForm">
                <div class="otp-container">
                    <input type="text" maxlength="1" class="otp-digit" id="digit1" autofocus>
                    <input type="text" maxlength="1" class="otp-digit" id="digit2">
                    <input type="text" maxlength="1" class="otp-digit" id="digit3">
                    <input type="text" maxlength="1" class="otp-digit" id="digit4">
                    <input type="text" maxlength="1" class="otp-digit" id="digit5">
                    <input type="text" maxlength="1" class="otp-digit" id="digit6">
                </div>
                <input type="hidden" name="otp_code" id="otp_code">
                <input type="hidden" name="verify" value="1">
                
                <button type="submit" class="btn btn-primary btn-block verify-btn" id="verifyBtn">
                    Verify OTP
                </button>
            </form>
            
            <div class="timer" id="timer">
                <span id="timerText"></span>
            </div>
            
            <div style="text-align: center; margin-top: 15px;">
                <form method="POST" action="" id="resendForm" style="display: inline;">
                    <button type="submit" name="resend" class="resend-btn" id="resendBtn">Resend OTP</button>
                </form>
            </div>
            
            <div class="form-footer">
                <p><a href="register.php">← Back to Registration</a></p>
            </div>
        </div>
    </div>
    
    <script>
        // Get all OTP digit inputs
        const digits = [
            document.getElementById('digit1'),
            document.getElementById('digit2'),
            document.getElementById('digit3'),
            document.getElementById('digit4'),
            document.getElementById('digit5'),
            document.getElementById('digit6')
        ];
        
        const otpHidden = document.getElementById('otp_code');
        const verifyBtn = document.getElementById('verifyBtn');
        const otpForm = document.getElementById('otpForm');
        const resendBtn = document.getElementById('resendBtn');
        
        // Handle digit input
        function handleDigitInput(e, index) {
            const input = e.target;
            let value = input.value;
            
            // Only allow numbers
            value = value.replace(/[^0-9]/g, '');
            input.value = value;
            
            // Auto move to next field
            if (value.length === 1 && index < 5) {
                digits[index + 1].focus();
            }
            
            // Update hidden OTP value
            updateHiddenOTP();
            
            // Auto submit if all digits filled
            const allFilled = digits.every(digit => digit.value.length === 1);
            if (allFilled) {
                autoSubmitOTP();
            }
        }
        
        // Handle backspace key
        function handleKeyDown(e, index) {
            if (e.key === 'Backspace' && index > 0 && !digits[index].value) {
                digits[index - 1].focus();
                digits[index - 1].value = '';
                updateHiddenOTP();
            }
        }
        
        // Handle paste event
        function handlePaste(e, index) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text');
            const numbers = pastedData.replace(/[^0-9]/g, '').split('');
            
            for (let i = 0; i < Math.min(numbers.length, 6 - index); i++) {
                if (digits[index + i]) {
                    digits[index + i].value = numbers[i];
                }
            }
            
            updateHiddenOTP();
            
            // Focus on next empty field
            const nextEmpty = digits.find(digit => !digit.value);
            if (nextEmpty) {
                nextEmpty.focus();
            } else {
                autoSubmitOTP();
            }
        }
        
        // Update hidden OTP value
        function updateHiddenOTP() {
            const otpValue = digits.map(digit => digit.value).join('');
            otpHidden.value = otpValue;
        }
        
        // Auto submit OTP
        function autoSubmitOTP() {
            const otpValue = otpHidden.value;
            if (otpValue.length === 6) {
                // Show loading state
                verifyBtn.innerHTML = 'Verifying... <span class="spinner"></span>';
                verifyBtn.disabled = true;
                
                // Add loading class to form
                otpForm.classList.add('loading');
                
                // Submit form via AJAX to prevent page refresh
                submitOTPViaAJAX(otpValue);
            }
        }
        
        // Submit OTP via AJAX
        async function submitOTPViaAJAX(otp) {
            const formData = new FormData();
            formData.append('verify', '1');
            formData.append('otp_code', otp);
            
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                const html = await response.text();
                
                // Check if redirect occurred (dashboard)
                if (html.includes('dashboard') || response.redirected) {
                    window.location.href = 'dashboard.php';
                    return;
                }
                
                // Check for error messages in response
                if (html.includes('alert alert-error')) {
                    const errorMatch = html.match(/alert alert-error[^>]*>([^<]*)</);
                    if (errorMatch) {
                        showError(errorMatch[1].trim());
                    } else {
                        showError('Invalid OTP. Please try again.');
                    }
                    
                    // Reset form
                    resetOTPFields();
                } else if (html.includes('alert alert-success')) {
                    const successMatch = html.match(/alert alert-success[^>]*>([^<]*)</);
                    if (successMatch) {
                        showSuccess(successMatch[1].trim());
                    }
                    
                    // If verification successful, redirect
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 2000);
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Network error. Please try again.');
                resetOTPFields();
            } finally {
                // Reset loading state
                verifyBtn.innerHTML = 'Verify OTP';
                verifyBtn.disabled = false;
                otpForm.classList.remove('loading');
            }
        }
        
        // Reset OTP fields
        function resetOTPFields() {
            digits.forEach(digit => {
                digit.value = '';
            });
            otpHidden.value = '';
            digits[0].focus();
        }
        
        // Show error message
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.innerHTML = message;
            errorDiv.style.display = 'block';
            
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }
        
        // Show success message
        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            successDiv.innerHTML = message;
            successDiv.style.display = 'block';
            
            setTimeout(() => {
                successDiv.style.display = 'none';
            }, 3000);
        }
        
        // Add event listeners to digit inputs
        digits.forEach((digit, index) => {
            digit.addEventListener('input', (e) => handleDigitInput(e, index));
            digit.addEventListener('keydown', (e) => handleKeyDown(e, index));
            digit.addEventListener('paste', (e) => handlePaste(e, index));
        });
        
        // Prevent manual form submission (use AJAX instead)
        otpForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const otpValue = otpHidden.value;
            if (otpValue.length === 6) {
                submitOTPViaAJAX(otpValue);
            } else {
                showError('Please enter complete 6-digit OTP');
            }
        });
        
        // Timer for OTP expiry (10 minutes = 600 seconds)
        let timeLeft = 600;
        const timerElement = document.getElementById('timerText');
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `⏱️ OTP expires in: ${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                timerElement.textContent = '❌ OTP has expired. Please request a new one.';
                timerElement.style.color = '#ff4757';
                resendBtn.disabled = false;
                clearInterval(timerInterval);
                
                // Disable OTP inputs
                digits.forEach(digit => {
                    digit.disabled = true;
                });
                verifyBtn.disabled = true;
            } else {
                timeLeft--;
            }
        }
        
        // Start timer
        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);
        
        // Initially disable resend button for 30 seconds
        resendBtn.disabled = true;
        let resendCooldown = 30;
        const resendTimer = setInterval(() => {
            if (resendCooldown > 0) {
                resendBtn.textContent = `Resend OTP (${resendCooldown}s)`;
                resendBtn.disabled = true;
                resendCooldown--;
            } else {
                resendBtn.textContent = 'Resend OTP';
                resendBtn.disabled = false;
                clearInterval(resendTimer);
            }
        }, 1000);
        
        // Handle resend button click
        resendBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('resend', '1');
            
            resendBtn.disabled = true;
            resendBtn.textContent = 'Sending...';
            
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });
                
                const html = await response.text();
                
                if (html.includes('alert alert-success')) {
                    const successMatch = html.match(/alert alert-success[^>]*>([^<]*)</);
                    if (successMatch) {
                        showSuccess(successMatch[1].trim());
                    }
                    
                    // Reset timer
                    timeLeft = 600;
                    updateTimer();
                    
                    // Reset cooldown
                    resendCooldown = 30;
                    const newResendTimer = setInterval(() => {
                        if (resendCooldown > 0) {
                            resendBtn.textContent = `Resend OTP (${resendCooldown}s)`;
                            resendBtn.disabled = true;
                            resendCooldown--;
                        } else {
                            resendBtn.textContent = 'Resend OTP';
                            resendBtn.disabled = false;
                            clearInterval(newResendTimer);
                        }
                    }, 1000);
                    
                    // Enable OTP inputs
                    digits.forEach(digit => {
                        digit.disabled = false;
                        digit.value = '';
                    });
                    verifyBtn.disabled = false;
                    digits[0].focus();
                } else {
                    showError('Failed to resend OTP. Please try again.');
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend OTP';
                }
            } catch (error) {
                showError('Network error. Please try again.');
                resendBtn.disabled = false;
                resendBtn.textContent = 'Resend OTP';
            }
        });
        
        // Focus on first digit on page load
        digits[0].focus();
    </script>
</body>
</html>