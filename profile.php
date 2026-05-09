<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        $name = sanitizeInput($_POST['name']);
        
        $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        if ($stmt->execute([$name, $user_id])) {
            $_SESSION['user_name'] = $name;
            $message = 'Profile updated successfully';
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } else {
            $error = 'Failed to update profile';
        }
    }
}

// Change password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (!password_verify($current_password, $user['password'])) {
            $error = 'Current password is incorrect';
        } elseif (strlen($new_password) < 8) {
            $error = 'New password must be at least 8 characters';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashed_password, $user_id])) {
                $message = 'Password changed successfully';
            } else {
                $error = 'Failed to change password';
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
    <title>Profile - TrustNet Security</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet Security</div>
            <div class="nav-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="websites.php">Websites</a>
                <a href="analytics.php">Analytics</a>
                <a href="profile.php" class="active">Profile</a>
                <?php if ($_SESSION['user_role'] == 'admin'): ?>
                    <a href="admin-panel.php">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="glass-card" style="max-width: 600px; margin: 0 auto;">
            <h2>Profile Settings</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="update_profile" value="1">
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <small style="color: #7B61FF;">Email cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label>Account Type</label>
                    <input type="text" class="form-control" value="<?php echo ucfirst($user['role']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Member Since</label>
                    <input type="text" class="form-control" value="<?php echo date('F j, Y', strtotime($user['created_at'])); ?>" disabled>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
            </form>
            
            <hr style="margin: 30px 0; border-color: rgba(255,255,255,0.1);">
            
            <h3>Change Password</h3>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="change_password" value="1">
                
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                    <small>Minimum 8 characters</small>
                </div>
                
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Change Password</button>
            </form>
        </div>
    </div>
</body>
</html>