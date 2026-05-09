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

// Get user preferences (simplified - you can expand this)
$theme = $_COOKIE['trustnet_theme'] ?? 'dark';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['theme'])) {
        setcookie('trustnet_theme', $_POST['theme'], time() + (86400 * 30), "/");
        $theme = $_POST['theme'];
        $message = 'Settings saved successfully';
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - TrustNet Security</title>
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
                <a href="profile.php">Profile</a>
                <a href="settings.php" class="active">Settings</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="glass-card" style="max-width: 600px; margin: 0 auto;">
            <h2>Settings</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label>Theme Preference</label>
                    <select name="theme" class="form-control">
                        <option value="dark" <?php echo $theme == 'dark' ? 'selected' : ''; ?>>Dark Theme</option>
                        <option value="light" <?php echo $theme == 'light' ? 'selected' : ''; ?>>Light Theme</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Save Settings</button>
            </form>
            
            <hr style="margin: 30px 0; border-color: rgba(255,255,255,0.1);">
            
            <h3>API Documentation</h3>
            <div class="code-block">
                <pre>
// Login API Endpoint
POST /api/login.php
Content-Type: application/json

{
    "api_key": "YOUR_API_KEY",
    "secret_key": "YOUR_SECRET_KEY",
    "email": "user@example.com",
    "password": "user_password"
}

// Response for blocked account (4 failed attempts)
{
    "status": "blocked",
    "message": "Account blocked for 5 hours",
    "blocked_until": "2024-01-15 15:30:00"
}

// Tracking API Endpoint
POST /api/track.php
Content-Type: application/json

{
    "api_key": "YOUR_API_KEY",
    "action": "pageview",
    "page_url": "https://example.com/page",
    "referrer": "https://google.com"
}
                </pre>
            </div>
        </div>
    </div>
</body>
</html>