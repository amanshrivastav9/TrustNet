<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's websites
$stmt = $pdo->prepare("SELECT id, website_name FROM websites WHERE user_id = ?");
$stmt->execute([$user_id]);
$websites = $stmt->fetchAll();

$selected_website = $_GET['website_id'] ?? ($websites[0]['id'] ?? 0);

// Get geo analytics data
$geo_data = [];

if ($selected_website) {
    // Get visitors by country
    $stmt = $pdo->prepare("SELECT location, COUNT(*) as count 
                           FROM login_logs 
                           WHERE website_id = ? AND location != 'Unknown'
                           GROUP BY location 
                           ORDER BY count DESC 
                           LIMIT 20");
    $stmt->execute([$selected_website]);
    $geo_data['by_country'] = $stmt->fetchAll();
    
    // Get unique visitors by country
    $stmt = $pdo->prepare("SELECT location, COUNT(DISTINCT user_identifier) as unique_visitors 
                           FROM login_logs 
                           WHERE website_id = ? AND location != 'Unknown'
                           GROUP BY location 
                           ORDER BY unique_visitors DESC");
    $stmt->execute([$selected_website]);
    $geo_data['unique_by_country'] = $stmt->fetchAll();
    
    // Get recent visitor locations
    $stmt = $pdo->prepare("SELECT location, ip_address, browser, device, os, login_time 
                           FROM login_logs 
                           WHERE website_id = ? 
                           ORDER BY login_time DESC 
                           LIMIT 50");
    $stmt->execute([$selected_website]);
    $geo_data['recent_visitors'] = $stmt->fetchAll();
    
    // Get total visitors by location
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM login_logs WHERE website_id = ?");
    $stmt->execute([$selected_website]);
    $geo_data['total_visitors'] = $stmt->fetch()['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geo Analytics - TrustNet Security</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet Security</div>
            <div class="nav-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="websites.php">Websites</a>
                <a href="analytics.php">Analytics</a>
                <a href="geo-analytics.php" class="active">Geo Analytics</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="glass-card">
            <h2>Geographic Analytics</h2>
            
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
        
        <?php if ($selected_website && !empty($geo_data['by_country'])): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">🌍</div>
                    <div class="stat-value"><?php echo count($geo_data['by_country']); ?></div>
                    <div class="stat-label">Countries/Regions</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value"><?php echo $geo_data['total_visitors']; ?></div>
                    <div class="stat-label">Total Visitors</div>
                </div>
            </div>
            
            <div class="stats-grid" style="grid-template-columns: 1fr 1fr;">
                <div class="glass-card">
                    <h3>Visitors by Country</h3>
                    <canvas id="countryChart" style="max-height: 400px;"></canvas>
                </div>
                
                <div class="glass-card">
                    <h3>Top Visiting Countries</h3>
                    <div class="country-list">
                        <?php 
                        $total = array_sum(array_column($geo_data['by_country'], 'count'));
                        foreach (array_slice($geo_data['by_country'], 0, 10) as $country): 
                            $percentage = ($country['count'] / $total) * 100;
                            $country_name = explode(',', $country['location']);
                            $country_name = end($country_name);
                        ?>
                            <div class="country-item">
                                <div class="country-name">
                                    <?php echo getCountryFlag(substr($country['location'], -2)) ?? '🌍'; ?>
                                    <?php echo htmlspecialchars(trim($country_name)); ?>
                                </div>
                                <div class="country-stats">
                                    <span class="country-count"><?php echo $country['count']; ?> visits</span>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                    <span class="country-percentage"><?php echo round($percentage, 1); ?>%</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="glass-card">
                <h3>Recent Visitors Location Map</h3>
                <div id="simpleMap" style="height: 400px; background: rgba(0,0,0,0.3); border-radius: 10px; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <div style="font-size: 48px;">🗺️</div>
                        <p>World Map Visualization</p>
                        <small>For full map integration, upgrade to a paid mapping service</small>
                    </div>
                </div>
            </div>
            
            <div class="glass-card">
                <h3>Recent Visitors</h3>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Location</th>
                                <th>IP Address</th>
                                <th>Browser</th>
                                <th>Device</th>
                                <th>OS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($geo_data['recent_visitors'] as $visitor): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($visitor['login_time'])); ?></td>
                                    <td>
                                        <?php 
                                        $location_parts = explode(',', $visitor['location']);
                                        $country = trim(end($location_parts));
                                        $flag = getCountryFlag(substr($visitor['location'], -2)) ?? '🌍';
                                        echo $flag . ' ' . htmlspecialchars($country);
                                        ?>
                                    </td>
                                    <td><code><?php echo htmlspecialchars($visitor['ip_address']); ?></code></td>
                                    <td><?php echo htmlspecialchars($visitor['browser']); ?></td>
                                    <td><?php echo htmlspecialchars($visitor['device']); ?></td>
                                    <td><?php echo htmlspecialchars($visitor['os']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($selected_website): ?>
            <div class="glass-card" style="text-align: center;">
                <p>No geographic data available yet. Start getting visitors to see location analytics.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        document.getElementById('websiteSelect').addEventListener('change', function() {
            window.location.href = 'geo-analytics.php?website_id=' + this.value;
        });
        
        <?php if (!empty($geo_data['by_country'])): 
            $countries = [];
            $counts = [];
            $colors = ['#00D1FF', '#7B61FF', '#ff4757', '#ffa502', '#2ed573', '#ff6b81', '#ffb8b8', '#70a1ff', '#ff7f50', '#87ff65'];
            
            foreach (array_slice($geo_data['by_country'], 0, 10) as $index => $country):
                $country_name = explode(',', $country['location']);
                $countries[] = trim(end($country_name));
                $counts[] = $country['count'];
            endforeach;
        ?>
        const ctx = document.getElementById('countryChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($countries); ?>,
                datasets: [{
                    label: 'Visitor Count',
                    data: <?php echo json_encode($counts); ?>,
                    backgroundColor: <?php echo json_encode(array_slice($colors, 0, count($countries))); ?>,
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: { color: '#00D1FF' }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        color: '#ffffff',
                        font: { weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        ticks: { color: '#ffffff', beginAtZero: true },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    },
                    x: {
                        ticks: { color: '#ffffff' },
                        grid: { display: false }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
    
    <style>
        .country-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .country-item {
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }
        
        .country-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #00D1FF;
        }
        
        .country-stats {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .country-count {
            font-size: 12px;
            color: #7B61FF;
            min-width: 80px;
        }
        
        .progress-bar {
            flex: 1;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #00D1FF, #7B61FF);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .country-percentage {
            font-size: 12px;
            color: #00D1FF;
            min-width: 45px;
            text-align: right;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .data-table th {
            color: #00D1FF;
            font-weight: 600;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        code {
            background: rgba(0, 0, 0, 0.3);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</body>
</html>