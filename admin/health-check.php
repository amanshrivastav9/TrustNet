<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$checks = [];

// Check PHP version
$checks['php_version'] = [
    'name' => 'PHP Version',
    'status' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'value' => PHP_VERSION,
    'required' => '>= 7.4.0'
];

// Check MySQL connection
try {
    $pdo->query("SELECT 1");
    $checks['mysql'] = [
        'name' => 'MySQL Connection',
        'status' => true,
        'value' => 'Connected',
        'required' => 'Working'
    ];
} catch (Exception $e) {
    $checks['mysql'] = [
        'name' => 'MySQL Connection',
        'status' => false,
        'value' => $e->getMessage(),
        'required' => 'Working'
    ];
}

// Check required extensions
$extensions = ['pdo', 'pdo_mysql', 'openssl', 'json', 'curl', 'mbstring'];
foreach ($extensions as $ext) {
    $checks['ext_' . $ext] = [
        'name' => $ext . ' Extension',
        'status' => extension_loaded($ext),
        'value' => extension_loaded($ext) ? 'Loaded' : 'Not Loaded',
        'required' => 'Loaded'
    ];
}

// Check directories writable
$dirs = ['../assets', '../includes', '../logs'];
foreach ($dirs as $dir) {
    $full_path = __DIR__ . '/' . $dir;
    $checks['dir_' . $dir] = [
        'name' => 'Directory: ' . $dir,
        'status' => is_writable($full_path),
        'value' => is_writable($full_path) ? 'Writable' : 'Not Writable',
        'required' => 'Writable'
    ];
}

// Check API connectivity
$api_check = @file_get_contents('http://ip-api.com/json/8.8.8.8');
$checks['geo_api'] = [
    'name' => 'Geo Location API',
    'status' => $api_check !== false,
    'value' => $api_check !== false ? 'Available' : 'Unavailable',
    'required' => 'Available'
];

// Check session
session_start();
$checks['session'] = [
    'name' => 'Session Handling',
    'status' => session_status() === PHP_SESSION_ACTIVE,
    'value' => session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive',
    'required' => 'Active'
];

// Count total checks
$total_checks = count($checks);
$passed_checks = count(array_filter($checks, function($check) {
    return $check['status'] === true;
}));

$status = $passed_checks === $total_checks ? 'healthy' : ($passed_checks > $total_checks / 2 ? 'warning' : 'critical');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Health Check - TrustNet</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet Admin</div>
            <div class="nav-menu">
                <a href="../dashboard.php">Dashboard</a>
                <a href="../admin-panel.php">Admin Panel</a>
                <a href="health-check.php" class="active">Health Check</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="glass-card">
            <h2>System Health Check</h2>
            
            <div class="health-status">
                <div class="status-badge status-<?php echo $status; ?>">
                    System Status: <?php echo ucfirst($status); ?>
                </div>
                <div class="status-summary">
                    Passed: <?php echo $passed_checks; ?> / <?php echo $total_checks; ?> checks
                </div>
            </div>
            
            <table class="health-table">
                <thead>
                    <tr>
                        <th>Check</th>
                        <th>Status</th>
                        <th>Current Value</th>
                        <th>Required</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($checks as $check): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($check['name']); ?></td>
                            <td>
                                <span class="health-status-icon <?php echo $check['status'] ? 'success' : 'error'; ?>">
                                    <?php echo $check['status'] ? '✓' : '✗'; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($check['value']); ?></td>
                            <td><?php echo htmlspecialchars($check['required']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="recommendations">
                <h3>Recommendations</h3>
                <ul>
                    <?php if (!$checks['php_version']['status']): ?>
                        <li>Upgrade PHP to version 7.4 or higher for better performance and security</li>
                    <?php endif; ?>
                    
                    <?php if (!$checks['geo_api']['status']): ?>
                        <li>Check internet connection for Geo Location API access</li>
                    <?php endif; ?>
                    
                    <li>Enable HTTPS in production for secure data transmission</li>
                    <li>Set up regular database backups (recommended: daily)</li>
                    <li>Configure firewall to restrict API access to trusted IPs only</li>
                </ul>
            </div>
        </div>
    </div>
    
    <style>
        .health-status {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .status-healthy {
            background: rgba(0, 209, 255, 0.2);
            color: #00D1FF;
        }
        
        .status-warning {
            background: rgba(255, 165, 2, 0.2);
            color: #ffa502;
        }
        
        .status-critical {
            background: rgba(255, 71, 87, 0.2);
            color: #ff4757;
        }
        
        .health-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .health-table th,
        .health-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .health-table th {
            color: #00D1FF;
        }
        
        .health-status-icon {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
        }
        
        .health-status-icon.success {
            background: rgba(0, 209, 255, 0.2);
            color: #00D1FF;
        }
        
        .health-status-icon.error {
            background: rgba(255, 71, 87, 0.2);
            color: #ff4757;
        }
        
        .recommendations {
            background: rgba(0,0,0,0.2);
            padding: 20px;
            border-radius: 10px;
        }
        
        .recommendations ul {
            margin-left: 20px;
            margin-top: 10px;
        }
        
        .recommendations li {
            margin: 10px 0;
            color: #ffa502;
        }
    </style>
</body>
</html>