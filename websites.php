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

// Get user's websites only
$stmt = $pdo->prepare("SELECT * FROM websites WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$websites = $stmt->fetchAll();

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM websites WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    header('Location: websites.php');
    exit();
}

// Handle regenerate keys
if (isset($_GET['regenerate'])) {
    $id = $_GET['regenerate'];
    $new_api_key = generateApiKey();
    $new_secret_key = generateSecretKey();
    $stmt = $pdo->prepare("UPDATE websites SET api_key = ?, secret_key = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$new_api_key, $new_secret_key, $id, $user_id]);
    header('Location: websites.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Websites - TrustNet</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet Security</div>
            <div class="nav-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="websites.php" class="active">Websites</a>
                <a href="analytics.php">Analytics</a>
                <a href="profile.php">Profile</a>
                <?php if ($is_admin): ?>
                    <a href="admin-panel.php">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2>My Websites</h2>
            <a href="add-website.php" class="btn btn-primary">+ Add New Website</a>
        </div>
        
        <?php if (empty($websites)): ?>
            <div class="glass-card" style="text-align: center;">
                <p>You haven't registered any websites yet.</p>
                <a href="add-website.php" class="btn btn-primary" style="margin-top: 20px;">Register Your First Website</a>
            </div>
        <?php else: ?>
            <div class="websites-grid">
                <?php foreach ($websites as $website): 
                    // Get stats for this website
                    $stats = getWebsiteAnalytics($pdo, $website['id'], $user_id, $is_admin);
                ?>
                    <div class="glass-card website-card">
                        <div class="website-header">
                            <h3><?php echo htmlspecialchars($website['website_name']); ?></h3>
                            <span class="status-badge status-<?php echo $website['status']; ?>">
                                <?php echo ucfirst($website['status']); ?>
                            </span>
                        </div>
                        
                        <div class="website-url">
                            🌐 <?php echo htmlspecialchars($website['website_url']); ?>
                        </div>
                        
                        <div class="website-stats">
                            <div class="stat">
                                <span class="stat-value"><?php echo number_format($stats['total_visits']); ?></span>
                                <span class="stat-label">Visits</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value"><?php echo number_format($stats['unique_visitors']); ?></span>
                                <span class="stat-label">Unique</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value"><?php echo number_format($stats['pageviews']); ?></span>
                                <span class="stat-label">Views</span>
                            </div>
                        </div>
                        
                        <div class="website-actions">
                            <a href="website-dashboard.php?id=<?php echo $website['id']; ?>" class="btn btn-primary btn-sm">View Dashboard</a>
                            <a href="tracking-code.php?id=<?php echo $website['id']; ?>" class="btn btn-secondary btn-sm">Get Code</a>
                            <a href="?regenerate=<?php echo $website['id']; ?>" class="btn btn-warning btn-sm" onclick="return confirm('Regenerate API keys? Current integrations will stop working.')">Regenerate</a>
                            <a href="?delete=<?php echo $website['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this website? All data will be lost.')">Delete</a>
                        </div>
                        
                        <div class="website-api">
                            <small>API: <?php echo substr($website['api_key'], 0, 20); ?>...</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <style>
        .websites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 20px;
        }
        
        .website-card {
            padding: 20px;
        }
        
        .website-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .website-header h3 {
            color: #00D1FF;
            margin: 0;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-active {
            background: rgba(0, 209, 255, 0.2);
            color: #00D1FF;
        }
        
        .status-inactive {
            background: rgba(255, 71, 87, 0.2);
            color: #ff4757;
        }
        
        .website-url {
            color: #7B61FF;
            font-size: 13px;
            margin-bottom: 15px;
            word-break: break-all;
        }
        
        .website-stats {
            display: flex;
            gap: 20px;
            padding: 15px 0;
            border-top: 1px solid rgba(255,255,255,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 15px;
        }
        
        .stat {
            text-align: center;
            flex: 1;
        }
        
        .stat .stat-value {
            display: block;
            font-size: 20px;
            font-weight: bold;
            color: #00D1FF;
        }
        
        .stat .stat-label {
            font-size: 11px;
            color: #7B61FF;
        }
        
        .website-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffa502, #ff6348);
            color: white;
        }
        
        .website-api {
            font-size: 11px;
            color: #666;
            text-align: center;
            padding-top: 10px;
        }
    </style>
</body>
</html>