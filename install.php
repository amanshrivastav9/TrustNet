<?php
/**
 * TrustNet Installation Helper
 * Run this script to set up the database and configuration
 */

// Check if already installed
if (file_exists('includes/installed.lock')) {
    die('TrustNet is already installed. Delete includes/installed.lock to reinstall.');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($step == 1) {
        // Test database connection
        $host = $_POST['db_host'];
        $name = $_POST['db_name'];
        $user = $_POST['db_user'];
        $pass = $_POST['db_pass'];
        
        try {
            $test_pdo = new PDO("mysql:host={$host};charset=utf8mb4", $user, $pass);
            $test_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if not exists
            $test_pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}`");
            
            $_SESSION['install_db'] = compact('host', 'name', 'user', 'pass');
            header('Location: install.php?step=2');
            exit();
        } catch (PDOException $e) {
            $error = "Database connection failed: " . $e->getMessage();
        }
    } elseif ($step == 2) {
        // Import database
        require_once 'includes/config.php';
        
        try {
            $sql = file_get_contents('sql/database.sql');
            $sql_extra = file_get_contents('sql/extra_tables.sql');
            
            $pdo->exec($sql);
            $pdo->exec($sql_extra);
            
            $_SESSION['install_db_imported'] = true;
            header('Location: install.php?step=3');
            exit();
        } catch (PDOException $e) {
            $error = "Database import failed: " . $e->getMessage();
        }
    } elseif ($step == 3) {
        // Create admin user
        $admin_name = $_POST['admin_name'];
        $admin_email = $_POST['admin_email'];
        $admin_password = $_POST['admin_password'];
        
        if (strlen($admin_password) < 8) {
            $error = 'Password must be at least 8 characters';
        } else {
            require_once 'includes/config.php';
            require_once 'includes/functions.php';
            
            $hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, email_verified) 
                                   VALUES (?, ?, ?, 'admin', 1)");
            if ($stmt->execute([$admin_name, $admin_email, $hashed_password])) {
                // Create lock file
                file_put_contents('includes/installed.lock', date('Y-m-d H:i:s'));
                
                $success = 'Installation completed successfully!';
                header('Refresh: 3; url=login.php');
            } else {
                $error = 'Failed to create admin user';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrustNet Installation</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="glass-card" style="max-width: 600px;">
            <h1 style="text-align: center; color: #00D1FF;">TrustNet Installation</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <div class="install-steps">
                <div class="step <?php echo $step >= 1 ? 'active' : ''; ?>">1. Database</div>
                <div class="step <?php echo $step >= 2 ? 'active' : ''; ?>">2. Import</div>
                <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">3. Admin User</div>
            </div>
            
            <?php if ($step == 1): ?>
                <form method="POST">
                    <h3>Database Configuration</h3>
                    
                    <div class="form-group">
                        <label>Database Host</label>
                        <input type="text" name="db_host" class="form-control" value="localhost" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Database Name</label>
                        <input type="text" name="db_name" class="form-control" value="trustnet_db" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Database Username</label>
                        <input type="text" name="db_user" class="form-control" value="root" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Database Password</label>
                        <input type="password" name="db_pass" class="form-control" value="">
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Test Connection & Continue</button>
                </form>
            <?php endif; ?>
            
            <?php if ($step == 2): ?>
                <form method="POST">
                    <h3>Import Database Tables</h3>
                    <p>Click the button below to import the database structure.</p>
                    <button type="submit" class="btn btn-primary btn-block">Import Database</button>
                </form>
            <?php endif; ?>
            
            <?php if ($step == 3): ?>
                <form method="POST">
                    <h3>Create Admin Account</h3>
                    
                    <div class="form-group">
                        <label>Admin Name</label>
                        <input type="text" name="admin_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Admin Email</label>
                        <input type="email" name="admin_email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Admin Password (min 8 characters)</label>
                        <input type="password" name="admin_password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Complete Installation</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
        .install-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: rgba(255,255,255,0.05);
            border-radius: 5px;
            margin: 0 5px;
            color: #7B61FF;
        }
        
        .step.active {
            background: rgba(0, 209, 255, 0.2);
            color: #00D1FF;
        }
    </style>
</body>
</html>