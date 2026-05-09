<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';
$error = '';

// Add IP to blacklist
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_ip'])) {
    $ip_address = sanitizeInput($_POST['ip_address']);
    $reason = sanitizeInput($_POST['reason']);
    
    if (filter_var($ip_address, FILTER_VALIDATE_IP)) {
        $stmt = $pdo->prepare("INSERT INTO ip_blacklist (ip_address, reason, blocked_by) VALUES (?, ?, ?)");
        if ($stmt->execute([$ip_address, $reason, $_SESSION['user_id']])) {
            $message = 'IP address blocked successfully';
        } else {
            $error = 'IP already blacklisted';
        }
    } else {
        $error = 'Invalid IP address';
    }
}

// Remove IP from blacklist
if (isset($_GET['remove'])) {
    $ip_id = $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM ip_blacklist WHERE id = ?");
    $stmt->execute([$ip_id]);
    $message = 'IP removed from blacklist';
}

// Get blacklisted IPs
$stmt = $pdo->query("SELECT b.*, u.name as blocked_by_name 
                     FROM ip_blacklist b 
                     LEFT JOIN users u ON b.blocked_by = u.id 
                     ORDER BY b.created_at DESC");
$blacklisted_ips = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blacklist IPs - Admin Panel</title>
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
                <a href="blacklist-ips.php" class="active">IP Blacklist</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="glass-card" style="max-width: 600px; margin: 0 auto 30px;">
            <h3>Add IP to Blacklist</h3>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>IP Address</label>
                    <input type="text" name="ip_address" class="form-control" required 
                           placeholder="e.g., 192.168.1.1">
                </div>
                
                <div class="form-group">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control" rows="3" 
                              placeholder="Why is this IP being blacklisted?"></textarea>
                </div>
                
                <button type="submit" name="add_ip" class="btn btn-danger btn-block">Block IP</button>
            </form>
        </div>
        
        <div class="glass-card">
            <h3>Blacklisted IPs</h3>
            
            <?php if (empty($blacklisted_ips)): ?>
                <p>No IPs are currently blacklisted.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>IP Address</th>
                            <th>Reason</th>
                            <th>Blocked By</th>
                            <th>Blocked On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blacklisted_ips as $ip): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($ip['ip_address']); ?></code></td>
                                <td><?php echo htmlspecialchars($ip['reason']); ?></td>
                                <td><?php echo htmlspecialchars($ip['blocked_by_name']); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($ip['created_at'])); ?></td>
                                <td>
                                    <a href="?remove=<?php echo $ip['id']; ?>" 
                                       class="btn btn-primary btn-sm" 
                                       onclick="return confirm('Remove this IP from blacklist?')">Unblock</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
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
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        code {
            background: rgba(0, 0, 0, 0.3);
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</body>
</html>