<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';
$error = '';

// Handle actions
if (isset($_GET['action'])) {
    $website_id = $_GET['id'] ?? 0;
    
    switch ($_GET['action']) {
        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM websites WHERE id = ?");
            $stmt->execute([$website_id]);
            $message = 'Website deleted successfully';
            break;
            
        case 'deactivate':
            $stmt = $pdo->prepare("UPDATE websites SET status = 'inactive' WHERE id = ?");
            $stmt->execute([$website_id]);
            $message = 'Website deactivated';
            break;
            
        case 'activate':
            $stmt = $pdo->prepare("UPDATE websites SET status = 'active' WHERE id = ?");
            $stmt->execute([$website_id]);
            $message = 'Website activated';
            break;
    }
}

// Get all websites with user info
$stmt = $pdo->query("SELECT w.*, u.name as owner_name, u.email as owner_email 
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
    <title>Manage Websites - Admin Panel</title>
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
                <a href="manage-websites.php" class="active">Websites</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <h2>Manage Websites</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <div class="glass-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Website Name</th>
                        <th>URL</th>
                        <th>Owner</th>
                        <th>API Key</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($websites as $website): ?>
                        <tr>
                            <td><?php echo $website['id']; ?></td>
                            <td><?php echo htmlspecialchars($website['website_name']); ?></td>
                            <td><?php echo htmlspecialchars($website['website_url']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($website['owner_name']); ?><br>
                                <small><?php echo htmlspecialchars($website['owner_email']); ?></small>
                            </td>
                            <td><code><?php echo substr($website['api_key'], 0, 20); ?>...</code></td>
                            <td>
                                <span class="badge badge-<?php echo $website['status']; ?>">
                                    <?php echo ucfirst($website['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($website['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if ($website['status'] == 'active'): ?>
                                    <a href="?action=deactivate&id=<?php echo $website['id']; ?>" 
                                       class="btn btn-warning btn-sm">Deactivate</a>
                                <?php else: ?>
                                    <a href="?action=activate&id=<?php echo $website['id']; ?>" 
                                       class="btn btn-primary btn-sm">Activate</a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?php echo $website['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete this website? All associated data will be lost.')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
            margin: 0 2px;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffa502, #ff6348);
            color: white;
        }
        
        .action-buttons {
            white-space: nowrap;
        }
        
        code {
            background: rgba(0, 0, 0, 0.3);
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
        }
    </style>
</body>
</html>