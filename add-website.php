<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';
$new_website = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token';
    } else {
        $website_name = sanitizeInput($_POST['website_name']);
        $website_url = sanitizeInput($_POST['website_url']);
        
        if (empty($website_name) || empty($website_url)) {
            $error = 'All fields are required';
        } elseif (!filter_var($website_url, FILTER_VALIDATE_URL)) {
            $error = 'Invalid website URL';
        } else {
            // Generate API keys
            $api_key = generateApiKey();
            $secret_key = generateSecretKey();
            
            $stmt = $pdo->prepare("INSERT INTO websites (user_id, website_name, website_url, api_key, secret_key, status) 
                                   VALUES (?, ?, ?, ?, ?, 'active')");
            if ($stmt->execute([$user_id, $website_name, $website_url, $api_key, $secret_key])) {
                $new_website = [
                    'id' => $pdo->lastInsertId(),
                    'name' => $website_name,
                    'url' => $website_url,
                    'api_key' => $api_key,
                    'secret_key' => $secret_key
                ];
                $success = 'Website registered successfully!';
            } else {
                $error = 'Failed to register website';
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
    <title>Add Website - TrustNet Security</title>
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
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="glass-card" style="max-width: 600px; margin: 0 auto;">
            <h2>Register New Website</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success && $new_website): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                
                <div class="code-block">
                    <h3>Your API Credentials</h3>
                    <p><strong>API Key:</strong> <code><?php echo $new_website['api_key']; ?></code></p>
                    <p><strong>Secret Key:</strong> <code><?php echo $new_website['secret_key']; ?></code></p>
                    <p style="color: #ff4757; margin-top: 10px;">
                        ⚠️ Save these keys now! Secret key will not be shown again.
                    </p>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label>Website Name</label>
                    <input type="text" name="website_name" class="form-control" required 
                           placeholder="e.g., My Awesome Website">
                </div>
                
                <div class="form-group">
                    <label>Website URL</label>
                    <input type="url" name="website_url" class="form-control" required 
                           placeholder="https://example.com">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Register Website</button>
            </form>
        </div>
    </div>
</body>
</html>