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

// Calculate total stats
$total_websites = count($websites);
$total_api_calls = 0;
$total_visits = 0;
$total_blocked = 0;

foreach ($websites as $website) {
    $stats = getWebsiteAnalytics($pdo, $website['id'], $user_id, $is_admin);
    if ($stats) {
        $total_visits += $stats['total_visits'];
        $total_blocked += $stats['blocked_users'];
    }
}

// Get total API calls
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM api_requests");
$stmt->execute();
$total_api_calls = $stmt->fetch()['count'];

// Get recent activity
$stmt = $pdo->prepare("SELECT al.*, w.website_name 
                       FROM activity_logs al 
                       LEFT JOIN websites w ON al.website_id = w.id 
                       WHERE w.user_id = ? OR ? = 1
                       ORDER BY al.timestamp DESC LIMIT 10");
$stmt->execute([$user_id, $is_admin]);
$recent_activity = $stmt->fetchAll();

// Get chart data
$stmt = $pdo->prepare("SELECT DATE(login_time) as date, COUNT(*) as count 
                       FROM login_logs ll
                       LEFT JOIN websites w ON ll.website_id = w.id
                       WHERE w.user_id = ? OR ? = 1
                       AND login_time > DATE_SUB(NOW(), INTERVAL 7 DAY)
                       GROUP BY DATE(login_time)");
$stmt->execute([$user_id, $is_admin]);
$chart_data = $stmt->fetchAll();

$dates = [];
$counts = [];
foreach ($chart_data as $data) {
    $dates[] = $data['date'];
    $counts[] = $data['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TrustNet Security</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet Security</div>
            <div class="nav-menu">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="websites.php">Websites</a>
                <a href="analytics.php">Analytics</a>
                <a href="security-dashboard.php">Security</a>
                <a href="profile.php">Profile</a>
                <?php if ($is_admin): ?>
                    <a href="admin-panel.php">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🌐</div>
                <div class="stat-value"><?php echo $total_websites; ?></div>
                <div class="stat-label">Total Websites</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-value"><?php echo number_format($total_api_calls); ?></div>
                <div class="stat-label">API Calls</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?php echo number_format($total_visits); ?></div>
                <div class="stat-label">Total Visits</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🚫</div>
                <div class="stat-value"><?php echo $total_blocked; ?></div>
                <div class="stat-label">Blocked Users</div>
            </div>
        </div>
        
        <?php if (!empty($dates)): ?>
        <div class="chart-container">
            <div class="glass-card">
                <h3>Login Activity (Last 7 Days)</h3>
                <canvas id="loginChart"></canvas>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="recent-activity">
            <div class="glass-card">
                <h3>Recent Activity</h3>
                <?php if (empty($recent_activity)): ?>
                    <p>No recent activity found.</p>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recent_activity as $activity): ?>
                            <div class="activity-item">
                                <span class="activity-time"><?php echo date('Y-m-d H:i:s', strtotime($activity['timestamp'])); ?></span>
                                <span class="activity-type"><?php echo htmlspecialchars($activity['activity_type']); ?></span>
                                <span class="activity-details"><?php echo htmlspecialchars(substr($activity['activity_details'], 0, 100)); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        <?php if (!empty($dates)): ?>
        const ctx = document.getElementById('loginChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Logins',
                    data: <?php echo json_encode($counts); ?>,
                    borderColor: '#00D1FF',
                    backgroundColor: 'rgba(0, 209, 255, 0.1)',
                    tension: 0.4,
                    fill: true
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
                    y: { ticks: { color: '#ffffff' }, beginAtZero: true },
                    x: { ticks: { color: '#ffffff' } }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>