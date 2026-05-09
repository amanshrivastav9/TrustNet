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
$analytics = null;

if ($selected_website) {
    $analytics = getWebsiteAnalytics($pdo, $selected_website, $user_id, $is_admin);
    
    // Get daily stats
    $stmt = $pdo->prepare("SELECT date, visits FROM website_stats WHERE website_id = ? AND date > DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY date ASC");
    $stmt->execute([$selected_website]);
    $daily_stats = $stmt->fetchAll();
    
    $dates = [];
    $visits = [];
    foreach ($daily_stats as $stat) {
        $dates[] = date('M d', strtotime($stat['date']));
        $visits[] = $stat['visits'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - TrustNet Security</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet Security</div>
            <div class="nav-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="websites.php">Websites</a>
                <a href="analytics.php" class="active">Analytics</a>
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
        <div class="glass-card">
            <h2>Analytics Dashboard</h2>
            
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
        
        <?php if ($selected_website && $analytics): ?>
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
            
            <?php if (!empty($dates)): ?>
            <div class="chart-container">
                <div class="glass-card">
                    <h3>Daily Visits (Last 30 Days)</h3>
                    <canvas id="visitsChart"></canvas>
                </div>
            </div>
            <?php endif; ?>
            
        <?php elseif ($selected_website): ?>
            <div class="glass-card" style="text-align: center;">
                <p>No analytics data available yet.</p>
                <a href="tracking-code.php?id=<?php echo $selected_website; ?>" class="btn btn-primary">Get Tracking Code</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        document.getElementById('websiteSelect').addEventListener('change', function() {
            window.location.href = 'analytics.php?website_id=' + this.value;
        });
        
        <?php if (!empty($dates)): ?>
        const ctx = document.getElementById('visitsChart').getContext('2d');
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
        <?php endif; ?>
    </script>
</body>
</html>