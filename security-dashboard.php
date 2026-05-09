<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$is_admin = ($user_role == 'admin');

// Get user's websites
$websites = getUserWebsites($pdo, $user_id, $is_admin);
$selected_website = $_GET['website_id'] ?? ($websites[0]['id'] ?? 0);

$blocked_users = [];
$failed_attempts = [];

if ($selected_website) {
    // Get blocked users
    $stmt = $pdo->prepare("SELECT * FROM api_user_blocks WHERE website_id = ? AND is_blocked = 1 ORDER BY blocked_until DESC");
    $stmt->execute([$selected_website]);
    $blocked_users = $stmt->fetchAll();
    
    // Get failed attempts
    $stmt = $pdo->prepare("SELECT user_identifier, COUNT(*) as attempts, MAX(attempt_time) as last_attempt 
                           FROM failed_login_attempts 
                           WHERE website_id = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                           GROUP BY user_identifier ORDER BY attempts DESC");
    $stmt->execute([$selected_website]);
    $failed_attempts = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Dashboard - TrustNet</title>
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
                <a href="security-dashboard.php" class="active">Security</a>
                <a href="profile.php">Profile</a>
                <?php if ($is_admin): ?>
                    <a href="admin-panel.php">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="glass-card">
            <h2>Security Dashboard</h2>
            
            <div class="form-group">
                <label>Select Website</label>
                <select id="websiteSelect" class="form-control">
                    <?php foreach ($websites as $website): ?>
                        <option value="<?php echo $website['id']; ?>" <?php echo $selected_website == $website['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($website['website_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="security-section">
            <div class="glass-card">
                <h3>🚫 Blocked Users (4 Failed Attempts = 5 Hours Block)</h3>
                <?php if (empty($blocked_users)): ?>
                    <p>No blocked users at this time.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr><th>Email</th><th>IP</th><th>Attempts</th><th>Blocked Until</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($blocked_users as $blocked): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($blocked['user_email']); ?></td>
                                <td><?php echo $blocked['ip_address']; ?></td>
                                <td><span class="badge badge-danger"><?php echo $blocked['failed_attempts']; ?></span></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($blocked['blocked_until'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="security-section">
            <div class="glass-card">
                <h3>⚠️ Recent Failed Login Attempts</h3>
                <?php if (empty($failed_attempts)): ?>
                    <p>No failed login attempts in the last 24 hours.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead><tr><th>Email</th><th>Attempts (24h)</th><th>Last Attempt</th></tr></thead>
                        <tbody>
                            <?php foreach ($failed_attempts as $attempt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($attempt['user_identifier']); ?></td>
                                <td><span class="badge badge-warning"><?php echo $attempt['attempts']; ?></span></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($attempt['last_attempt'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('websiteSelect').addEventListener('change', function() {
            window.location.href = 'security-dashboard.php?website_id=' + this.value;
        });
    </script>
    
    <style>
        .security-section { margin-bottom: 30px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .data-table th { color: #00D1FF; }
        .badge { padding: 4px 8px; border-radius: 5px; font-size: 12px; font-weight: bold; }
        .badge-danger { background: rgba(255,71,87,0.2); color: #ff4757; }
        .badge-warning { background: rgba(255,165,2,0.2); color: #ffa502; }
    </style>
</body>
</html>