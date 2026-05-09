<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';
$error = '';

// Handle user actions
if (isset($_GET['action'])) {
    $user_id = $_GET['id'] ?? 0;
    
    switch ($_GET['action']) {
        case 'delete':
            if ($user_id != $_SESSION['user_id']) {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $message = 'User deleted successfully';
            } else {
                $error = 'Cannot delete your own account';
            }
            break;
            
        case 'make_admin':
            $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
            $stmt->execute([$user_id]);
            $message = 'User promoted to admin';
            break;
            
        case 'make_user':
            $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ?");
            $stmt->execute([$user_id]);
            $message = 'Admin demoted to user';
            break;
            
        case 'block':
            $stmt = $pdo->prepare("UPDATE users SET status = 'blocked' WHERE id = ?");
            $stmt->execute([$user_id]);
            $message = 'User blocked';
            break;
    }
}

// Get all users
$stmt = $pdo->query("SELECT u.*, COUNT(w.id) as website_count 
                     FROM users u 
                     LEFT JOIN websites w ON u.id = w.user_id 
                     GROUP BY u.id 
                     ORDER BY u.created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet Admin</div>
            <div class="nav-menu">
                <a href="../dashboard.php">Dashboard</a>
                <a href="../admin-panel.php">Admin Panel</a>
                <a href="manage-users.php" class="active">Users</a>
                <a href="manage-websites.php">Websites</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2>Manage Users</h2>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="glass-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Websites</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo $user['website_count']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            <td class="action-buttons">
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <?php if ($user['role'] == 'admin'): ?>
                                        <a href="?action=make_user&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-warning btn-sm">Downgrade</a>
                                    <?php else: ?>
                                        <a href="?action=make_admin&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-primary btn-sm">Make Admin</a>
                                    <?php endif; ?>
                                    <a href="?action=delete&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Delete this user? All their websites will also be deleted.')">Delete</a>
                                <?php endif; ?>
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
        
        .badge-admin {
            background: rgba(0, 209, 255, 0.2);
            color: #00D1FF;
        }
        
        .badge-user {
            background: rgba(123, 97, 255, 0.2);
            color: #7B61FF;
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
    </style>
</body>
</html>