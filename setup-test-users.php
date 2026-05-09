<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<h1>Setting Up Test Users for TrustNet</h1>";

// First, check if tables exist and create them if needed
echo "<h2>Checking Database Tables...</h2>";

$tables_to_create = [
    "website_users" => "CREATE TABLE IF NOT EXISTS website_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        website_id INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(255),
        status ENUM('active', 'blocked') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login DATETIME,
        FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
        UNIQUE KEY unique_website_email (website_id, email)
    )",
    "failed_login_attempts" => "CREATE TABLE IF NOT EXISTS failed_login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        website_id INT NOT NULL,
        user_identifier VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45),
        location VARCHAR(255),
        user_agent TEXT,
        attempt_time DATETIME,
        INDEX idx_website_user (website_id, user_identifier)
    )",
    "security_events" => "CREATE TABLE IF NOT EXISTS security_events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        website_id INT NOT NULL,
        event_type VARCHAR(50) NOT NULL,
        severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
        user_identifier VARCHAR(255),
        ip_address VARCHAR(45),
        location VARCHAR(255),
        event_details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables_to_create as $table_name => $sql) {
    try {
        $pdo->exec($sql);
        echo "✓ Table '{$table_name}' is ready<br>";
    } catch (PDOException $e) {
        echo "✗ Error creating '{$table_name}': " . $e->getMessage() . "<br>";
    }
}

// Get all websites
$stmt = $pdo->query("SELECT id, website_name, api_key, secret_key FROM websites");
$websites = $stmt->fetchAll();

if (empty($websites)) {
    echo "<p style='color: red;'>No websites found. Please create a website first from TrustNet dashboard.</p>";
    echo "<a href='add-website.php' class='btn btn-primary'>Add Website</a>";
    exit();
}

echo "<h2>Adding Test Users to Websites</h2>";

foreach ($websites as $website) {
    echo "<div style='border:1px solid #00D1FF; padding:15px; margin:10px 0; border-radius:10px;'>";
    echo "<h3>Website: {$website['website_name']}</h3>";
    echo "<strong>API Key:</strong> <code>{$website['api_key']}</code><br>";
    echo "<strong>Secret Key:</strong> <code>{$website['secret_key']}</code><br><br>";
    
    // Check if users exist
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM website_users WHERE website_id = ?");
    $stmt->execute([$website['id']]);
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        // Add test users with different passwords for testing
        $test_users = [
            ['test@example.com', 'Test User', 'password123'],
            ['demo@example.com', 'Demo User', 'demo123'],
            ['user@test.com', 'Regular User', 'user123'],
            ['admin@test.com', 'Admin User', 'admin123']
        ];
        
        $added = 0;
        foreach ($test_users as $user) {
            $hashed_password = password_hash($user[2], PASSWORD_BCRYPT);
            try {
                $stmt = $pdo->prepare("INSERT INTO website_users (website_id, email, name, password) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$website['id'], $user[0], $user[1], $hashed_password])) {
                    echo "✓ Added user: <strong>{$user[0]}</strong> / Password: <code>{$user[2]}</code><br>";
                    $added++;
                }
            } catch (PDOException $e) {
                echo "✗ Failed to add {$user[0]}: " . $e->getMessage() . "<br>";
            }
        }
        echo "<span style='color: green;'>✓ Total {$added} users added</span><br>";
    } else {
        // Display existing users
        $stmt = $pdo->prepare("SELECT email, name FROM website_users WHERE website_id = ?");
        $stmt->execute([$website['id']]);
        $users = $stmt->fetchAll();
        echo "<span style='color: #ffa502;'>⚠️ Users already exist for this website:</span><br>";
        foreach ($users as $user) {
            echo "  - {$user['email']} ({$user['name']})<br>";
        }
    }
    
    echo "</div>";
}

// Display test instructions
echo "<div style='background: rgba(0,209,255,0.1); padding:20px; border-radius:10px; margin-top:20px;'>";
echo "<h2>🧪 Testing Instructions</h2>";
echo "<h3>Option 1: Using Curl Command (Open Command Prompt)</h3>";
echo "<pre style='background:#0A0F1C; padding:15px; overflow-x:auto;'>";
echo "curl -X POST http://localhost/trustnet/api/login.php \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d \"{\\\"api_key\\\":\\\"" . ($websites[0]['api_key'] ?? 'YOUR_API_KEY') . "\\\",\\\"secret_key\\\":\\\"" . ($websites[0]['secret_key'] ?? 'YOUR_SECRET_KEY') . "\\\",\\\"email\\\":\\\"test@example.com\\\",\\\"password\\\":\\\"wrongpassword\\\"}\"\n";
echo "</pre>";

echo "<h3>Option 2: Using JavaScript (Browser Console)</h3>";
echo "<pre style='background:#0A0F1C; padding:15px; overflow-x:auto;'>";
echo "fetch('http://localhost/trustnet/api/login.php', {\n";
echo "  method: 'POST',\n";
echo "  headers: {'Content-Type': 'application/json'},\n";
echo "  body: JSON.stringify({\n";
echo "    api_key: '" . ($websites[0]['api_key'] ?? 'YOUR_API_KEY') . "',\n";
echo "    secret_key: '" . ($websites[0]['secret_key'] ?? 'YOUR_SECRET_KEY') . "',\n";
echo "    email: 'test@example.com',\n";
echo "    password: 'wrongpassword'\n";
echo "  })\n";
echo "}).then(r => r.json()).then(console.log)\n";
echo "</pre>";

echo "<h3>Option 3: Run Test Script</h3>";
echo "<a href='test-blocking.php' class='btn btn-primary'>Run Automated Test</a>";

echo "<h3>Expected Results:</h3>";
echo "<ul>";
echo "<li><strong>Attempt 1-3:</strong> {<span style='color:#ffa502'>\"status\": \"error\"</span>, \"attempts_remaining\": 3,2,1}</li>";
echo "<li><strong>Attempt 4:</strong> {<span style='color:#ffa502'>\"status\": \"error\"</span>, \"attempts_remaining\": 0}</li>";
echo "<li><strong>Attempt 5:</strong> {<span style='color:#ff4757'>\"status\": \"blocked\"</span>, \"blocked_until\": \"...\", \"remaining_hours\": 5}</li>";
echo "</ul>";
echo "</div>";

// Display current blocked users
echo "<div style='margin-top:20px;'>";
echo "<h2>🚫 Currently Blocked Users</h2>";
$stmt = $pdo->query("SELECT * FROM api_user_blocks WHERE is_blocked = 1 AND blocked_until > NOW()");
$blocked = $stmt->fetchAll();
if ($blocked) {
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
    echo "<tr><th>Email</th><th>IP</th><th>Attempts</th><th>Blocked Until</th></tr>";
    foreach ($blocked as $b) {
        echo "<tr>";
        echo "<td>{$b['user_email']}</td>";
        echo "<td>{$b['ip_address']}</td>";
        echo "<td>{$b['failed_attempts']}</td>";
        echo "<td>{$b['blocked_until']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No blocked users at this time.</p>";
}
echo "</div>";
?>