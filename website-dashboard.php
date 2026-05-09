<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$is_admin = ($user_role == 'admin');

// Get website ID from URL
$website_id = $_GET['id'] ?? 0;

if (!$website_id) {
    header('Location: websites.php');
    exit();
}

// Verify ownership
if (!$is_admin) {
    $stmt = $pdo->prepare("SELECT * FROM websites WHERE id = ? AND user_id = ?");
    $stmt->execute([$website_id, $user_id]);
    $website = $stmt->fetch();
    if (!$website) {
        header('Location: websites.php');
        exit();
    }
} else {
    $stmt = $pdo->prepare("SELECT w.*, u.name as owner_name FROM websites w 
                           LEFT JOIN users u ON w.user_id = u.id 
                           WHERE w.id = ?");
    $stmt->execute([$website_id]);
    $website = $stmt->fetch();
}

// Get analytics data for this website
$analytics = getWebsiteAnalytics($pdo, $website_id, $user_id, $is_admin);

// Get recent activity
$stmt = $pdo->prepare("SELECT * FROM activity_logs 
                       WHERE website_id = ? 
                       ORDER BY timestamp DESC LIMIT 20");
$stmt->execute([$website_id]);
$recent_activity = $stmt->fetchAll();

// Get blocked users for this website
$stmt = $pdo->prepare("SELECT * FROM api_user_blocks 
                       WHERE website_id = ? AND is_blocked = 1 
                       ORDER BY blocked_until DESC");
$stmt->execute([$website_id]);
$blocked_users = $stmt->fetchAll();

// Get daily stats for chart
$stmt = $pdo->prepare("SELECT date, visits, pageviews 
                       FROM website_stats 
                       WHERE website_id = ? 
                       AND date > DATE_SUB(NOW(), INTERVAL 30 DAY)
                       ORDER BY date ASC");
$stmt->execute([$website_id]);
$daily_stats = $stmt->fetchAll();

$dates = [];
$visits = [];
foreach ($daily_stats as $stat) {
    $dates[] = date('M d', strtotime($stat['date']));
    $visits[] = $stat['visits'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($website['website_name']); ?> - Website Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet - <?php echo htmlspecialchars($website['website_name']); ?></div>
            <div class="nav-menu">
                <a href="dashboard.php">My Dashboard</a>
                <a href="websites.php">Websites</a>
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
        <div class="website-header">
            <h2><?php echo htmlspecialchars($website['website_name']); ?></h2>
            <p>URL: <?php echo htmlspecialchars($website['website_url']); ?></p>
            <div class="api-info">
                <code>API Key: <?php echo substr($website['api_key'], 0, 30); ?>...</code>
                <button onclick="copyToClipboard('<?php echo $website['api_key']; ?>')" class="btn btn-sm">Copy</button>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?php echo number_format($analytics['total_visits']); ?></div>
                <div class="stat-label">Total Visits</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🆕</div>
                <div class="stat-value"><?php echo number_format($analytics['unique_visitors']); ?></div>
                <div class="stat-label">Unique Visitors</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📄</div>
                <div class="stat-value"><?php echo number_format($analytics['pageviews']); ?></div>
                <div class="stat-label">Page Views</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🚫</div>
                <div class="stat-value"><?php echo $analytics['blocked_users']; ?></div>
                <div class="stat-label">Blocked Users</div>
            </div>
        </div>
        
        <div class="chart-container">
            <div class="glass-card">
                <h3>Visitors Trend (Last 30 Days)</h3>
                <canvas id="visitorsChart"></canvas>
            </div>
        </div>
        
        <div class="two-columns">
            <div class="glass-card">
                <h3>Recent Activity</h3>
                <div class="activity-list">
                    <?php foreach ($recent_activity as $activity): ?>
                        <div class="activity-item">
                            <span class="activity-time"><?php echo date('H:i:s', strtotime($activity['timestamp'])); ?></span>
                            <span class="activity-type"><?php echo $activity['activity_type']; ?></span>
                            <span class="activity-details"><?php echo htmlspecialchars(substr($activity['activity_details'], 0, 100)); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="glass-card">
                <h3>Blocked Users (5 hours block)</h3>
                <div class="blocked-list">
                    <?php if (empty($blocked_users)): ?>
                        <p>No blocked users</p>
                    <?php else: ?>
                        <?php foreach ($blocked_users as $blocked): ?>
                            <div class="blocked-item">
                                <div class="blocked-email">📧 <?php echo htmlspecialchars($blocked['user_email']); ?></div>
                                <div class="blocked-until">Blocked until: <?php echo date('Y-m-d H:i:s', strtotime($blocked['blocked_until'])); ?></div>
                                <div class="blocked-attempts">Failed attempts: <?php echo $blocked['failed_attempts']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="glass-card">
            <h3>Tracking Code</h3>
            <p>Copy this code and paste it into your website before the closing <code>&lt;/body&gt;</code> tag:</p>
            <div class="code-block">
                <pre><?php echo htmlspecialchars('
<script src="http://localhost/trustnet/assets/js/tracker.js" data-api-key="' . $website['api_key'] . '"></script>
                '); ?></pre>
            </div>
            <button onclick="copyToClipboard('<script src=\"http://localhost/trustnet/assets/js/tracker.js\" data-api-key=\"<?php echo $website['api_key']; ?>\"><\/script>')" class="btn btn-primary">Copy Code</button>
        </div>
    </div>
    
    <script>
        // Chart
        const ctx = document.getElementById('visitorsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Daily Visits',
                    data: <?php echo json_encode($visits); ?>,
                    borderColor: '#00D1FF',
                    backgroundColor: 'rgba(0, 209, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#00D1FF' } }
                },
                scales: {
                    y: { ticks: { color: '#ffffff' }, beginAtZero: true },
                    x: { ticks: { color: '#ffffff' } }
                }
            }
        });
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied to clipboard!');
            });
        }
    </script>
    
    <style>
        .website-header {
            background: rgba(0, 209, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .api-info {
            margin-top: 10px;
            padding: 10px;
            background: rgba(0,0,0,0.3);
            border-radius: 5px;
        }
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .blocked-item {
            padding: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .blocked-email { color: #ff4757; font-weight: bold; }
        .blocked-until { font-size: 12px; color: #ffa502; }
        .blocked-attempts { font-size: 12px; color: #7B61FF; }
        .btn-sm { padding: 5px 10px; font-size: 12px; margin-left: 10px; }
        @media (max-width: 768px) {
            .two-columns { grid-template-columns: 1fr; }
        }
    </style>
</body>
</html>