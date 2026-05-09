<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Get admin statistics
$stats = [];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $stmt->fetch()['count'];

// Total websites
$stmt = $pdo->query("SELECT COUNT(*) as count FROM websites");
$stats['total_websites'] = $stmt->fetch()['count'];

// Total API calls
$stmt = $pdo->query("SELECT COUNT(*) as count FROM api_requests");
$stats['total_api_calls'] = $stmt->fetch()['count'];

// Blocked users
$stmt = $pdo->query("SELECT COUNT(*) as count FROM api_user_blocks WHERE is_blocked = 1");
$stats['blocked_users'] = $stmt->fetch()['count'];

// Blacklisted IPs
$stmt = $pdo->query("SELECT COUNT(*) as count FROM ip_blacklist");
$stats['blacklisted_ips'] = $stmt->fetch()['count'];

// Recent activities
$stmt = $pdo->query("SELECT al.*, u.name as user_name, w.website_name 
                     FROM activity_logs al 
                     LEFT JOIN websites w ON al.website_id = w.id 
                     LEFT JOIN users u ON w.user_id = u.id 
                     ORDER BY al.timestamp DESC LIMIT 10");
$recent_activities = $stmt->fetchAll();

// Monthly API calls for chart
$stmt = $pdo->query("SELECT DATE_FORMAT(request_time, '%Y-%m') as month, COUNT(*) as count 
                     FROM api_requests 
                     WHERE request_time > DATE_SUB(NOW(), INTERVAL 6 MONTH)
                     GROUP BY DATE_FORMAT(request_time, '%Y-%m')
                     ORDER BY month ASC");
$chart_data = $stmt->fetchAll();

$months = [];
$counts = [];
foreach ($chart_data as $data) {
    $months[] = date('M Y', strtotime($data['month'] . '-01'));
    $counts[] = $data['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - TrustNet Security</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet Admin</div>
            <div class="nav-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="admin-panel.php" class="active">Admin Panel</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <h2>Admin Dashboard</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🌐</div>
                <div class="stat-value"><?php echo $stats['total_websites']; ?></div>
                <div class="stat-label">Total Websites</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value"><?php echo $stats['total_api_calls']; ?></div>
                <div class="stat-label">API Calls</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🚫</div>
                <div class="stat-value"><?php echo $stats['blocked_users']; ?></div>
                <div class="stat-label">Blocked Users</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🖤</div>
                <div class="stat-value"><?php echo $stats['blacklisted_ips']; ?></div>
                <div class="stat-label">Blacklisted IPs</div>
            </div>
        </div>
        
        <div class="admin-grid">
            <div class="glass-card">
                <h3>Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="admin/manage-users.php" class="btn btn-primary">Manage Users</a>
                    <a href="admin/manage-websites.php" class="btn btn-primary">Manage Websites</a>
                    <a href="admin/blacklist-ips.php" class="btn btn-danger">Blacklist IPs</a>
                    <a href="admin/system-logs.php" class="btn btn-secondary">View System Logs</a>
                    <a href="admin/api-keys.php" class="btn btn-secondary">Manage API Keys</a>
                </div>
            </div>
            
            <div class="glass-card">
                <h3>API Usage (Last 6 Months)</h3>
                <canvas id="apiChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
        
        <div class="glass-card">
            <h3>Recent System Activity</h3>
            <div class="activity-list">
                <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item">
                        <span class="activity-time"><?php echo date('Y-m-d H:i:s', strtotime($activity['timestamp'])); ?></span>
                        <span class="activity-type"><?php echo htmlspecialchars($activity['activity_type']); ?></span>
                        <span class="activity-details">
                            <?php echo htmlspecialchars(substr($activity['activity_details'], 0, 100)); ?>
                            <?php if ($activity['website_name']): ?>
                                (<?php echo htmlspecialchars($activity['website_name']); ?>)
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <script>
        const ctx = document.getElementById('apiChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'API Calls',
                    data: <?php echo json_encode($counts); ?>,
                    backgroundColor: 'rgba(0, 209, 255, 0.5)',
                    borderColor: '#00D1FF',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: '#00D1FF' }
                    }
                },
                scales: {
                    y: {
                        ticks: { color: '#ffffff' },
                        beginAtZero: true
                    },
                    x: {
                        ticks: { color: '#ffffff' }
                    }
                }
            }
        });
    </script>
    
    <style>
        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>