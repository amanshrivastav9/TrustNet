<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';

// Reset API key
if (isset($_GET['reset_key']) && isset($_GET['id'])) {
    $website_id = $_GET['id'];
    $new_api_key = generateApiKey();
    $new_secret_key = generateSecretKey();
    
    $stmt = $pdo->prepare("UPDATE websites SET api_key = ?, secret_key = ? WHERE id = ?");
    $stmt->execute([$new_api_key, $new_secret_key, $website_id]);
    $message = 'API keys regenerated successfully';
}

// Get all API keys
$stmt = $pdo->query("SELECT w.id, w.website_name, w.website_url, w.api_key, w.secret_key, w.status, u.name as owner_name 
                     FROM websites w 
                     LEFT JOIN users u ON w.user_id = u.id 
                     ORDER BY w.created_at DESC");
$websites = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Keys Management - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet Admin</div>
            <div class="nav-menu">
                <a href="../dashboard.php">Dashboard</a>
                <a href="../admin-panel.php">Admin Panel</a>
                <a href="manage-users.php">Users</a>
                <a href="manage-websites.php">Websites</a>
                <a href="api-keys.php" class="active">API Keys</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <h2>API Keys Management</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="glass-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Website</th>
                        <th>Owner</th>
                        <th>API Key</th>
                        <th>Secret Key</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($websites as $website): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($website['website_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($website['website_url']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($website['owner_name']); ?></td>
                            <td>
                                <code class="api-key"><?php echo substr($website['api_key'], 0, 30); ?>...</code>
                                <button class="copy-btn" onclick="copyToClipboard('<?php echo $website['api_key']; ?>')">📋</button>
                            </td>
                            <td>
                                <code class="api-key"><?php echo substr($website['secret_key'], 0, 30); ?>...</code>
                                <button class="copy-btn" onclick="copyToClipboard('<?php echo $website['secret_key']; ?>')">📋</button>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $website['status']; ?>">
                                    <?php echo ucfirst($website['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="?reset_key=1&id=<?php echo $website['id']; ?>" 
                                   class="btn btn-warning btn-sm"
                                   onclick="return confirm('Reset API keys? Current integrations will stop working.')">
                                    Regenerate
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="glass-card" style="margin-top: 20px;">
            <h3>API Usage Statistics</h3>
            <?php
            $stmt = $pdo->query("SELECT DATE(request_time) as date, COUNT(*) as count 
                                 FROM api_requests 
                                 WHERE request_time > DATE_SUB(NOW(), INTERVAL 7 DAY)
                                 GROUP BY DATE(request_time)
                                 ORDER BY date DESC");
            $api_stats = $stmt->fetchAll();
            ?>
            
            <?php if (!empty($api_stats)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total API Calls</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($api_stats as $stat): ?>
                            <tr>
                                <td><?php echo $stat['date']; ?></td>
                                <td><?php echo $stat['count']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No API calls recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied to clipboard!');
            });
        }
    </script>
    
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th,
        .admin-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-table th {
            color: #00D1FF;
            font-weight: 600;
        }
        
        .api-key {
            background: #0A0F1C;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
        }
        
        .copy-btn {
            background: rgba(0, 209, 255, 0.2);
            border: none;
            color: #00D1FF;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            margin-left: 5px;
        }
        
        .copy-btn:hover {
            background: rgba(0, 209, 255, 0.4);
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-active {
            background: rgba(0, 209, 255, 0.2);
            color: #00D1FF;
        }
        
        .badge-inactive {
            background: rgba(255, 71, 87, 0.2);
            color: #ff4757;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffa502, #ff6348);
            color: white;
        }
    </style>
</body>
</html>