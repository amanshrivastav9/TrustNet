<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if (!$user['email_verified']) {
                $error = 'Please verify your email first';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                session_regenerate_id(true);
                
                if ($user['role'] == 'admin') {
                    header('Location: admin-panel.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit();
            }
        } else {
            $error = 'Invalid email or password';
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
    <title>Login - TrustNet Security</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="glass-card">
            <div class="logo">
                <h1>TrustNet Security</h1>
                <p>Login to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required class="form-control" 
                           placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required class="form-control" 
                           placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
                
                <div class="form-footer">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>