<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$page = $_GET['page'] ?? 1;
$per_page = 50;
$offset = ($page - 1) * $per_page;

// Get total count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM activity_logs");
$total = $stmt->fetch()['total'];
$total_pages = ceil($total / $per_page);

// Get logs with pagination
$stmt = $pdo->prepare("SELECT al.*, w.website_name, u.name as user_name 
                       FROM activity_logs al 
                       LEFT JOIN websites w ON al.website_id = w.id 
                       LEFT JOIN users u ON w.user_id = u.id 
                       ORDER BY al.timestamp DESC 
                       LIMIT ? OFFSET ?");
$stmt->execute([$per_page, $offset]);
$logs = $stmt->fetchAll();

// Get filter options
$log_types = $pdo->query("SELECT DISTINCT activity_type FROM activity_logs")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs - Admin Panel</title>
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
                <a href="blacklist-ips.php">IP Blacklist</a>
                <a href="system-logs.php" class="active">Logs</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <h2>System Logs</h2>
        
        <div class="glass-card">
            <div class="log-filters" style="margin-bottom: 20px; display: flex; gap: 10px;">
                <select id="logType" class="form-control" style="width: auto;">
                    <option value="">All Log Types</option>
                    <?php foreach ($log_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type['activity_type']); ?>">
                            <?php echo ucfirst($type['activity_type']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="text" id="searchLog" class="form-control" placeholder="Search logs..." style="flex: 1;">
            </div>
            
            <div class="table-responsive">
                <table class="admin-table" id="logsTable">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Type</th>
                            <th>Website</th>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($log['timestamp'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $log['activity_type']; ?>">
                                        <?php echo htmlspecialchars($log['activity_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($log['website_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($log['user_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                                <td><?php echo htmlspecialchars(substr($log['activity_details'], 0, 100)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 20px; text-align: center;">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" 
                           class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-secondary'; ?> btn-sm"
                           style="margin: 0 2px;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Filter logs by type
        document.getElementById('logType').addEventListener('change', function() {
            filterTable();
        });
        
        // Search logs
        document.getElementById('searchLog').addEventListener('keyup', function() {
            filterTable();
        });
        
        function filterTable() {
            const type = document.getElementById('logType').value.toLowerCase();
            const search = document.getElementById('searchLog').value.toLowerCase();
            const rows = document.querySelectorAll('#logsTable tbody tr');
            
            rows.forEach(row => {
                const typeCell = row.cells[1].textContent.toLowerCase();
                const detailsCell = row.cells[5].textContent.toLowerCase();
                let show = true;
                
                if (type && !typeCell.includes(type)) {
                    show = false;
                }
                
                if (search && !detailsCell.includes(search) && !typeCell.includes(search)) {
                    show = false;
                }
                
                row.style.display = show ? '' : 'none';
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
        
        .badge {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .badge-pageview {
            background: rgba(0, 209, 255, 0.2);
            color: #00D1FF;
        }
        
        .badge-login {
            background: rgba(123, 97, 255, 0.2);
            color: #7B61FF;
        }
        
        .badge-failed_login {
            background: rgba(255, 71, 87, 0.2);
            color: #ff4757;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</body>
</html>