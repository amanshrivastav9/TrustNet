<?php
echo "<h1>Testing 4 Failed Attempts Blocking</h1>";

// Get first website's API key
require_once 'includes/config.php';
$stmt = $pdo->query("SELECT api_key, secret_key FROM websites LIMIT 1");
$website = $stmt->fetch();

if (!$website) {
    die("No website found. Please create a website first.");
}

$api_key = $website['api_key'];
$secret_key = $website['secret_key'];
$test_email = "test@example.com";
$wrong_password = "wrongpassword";

echo "<pre>";
echo "API Key: $api_key\n";
echo "Secret Key: $secret_key\n";
echo "Test Email: $test_email\n";
echo "Wrong Password: $wrong_password\n";
echo "\n=== TESTING 4 FAILED ATTEMPTS ===\n\n";

for ($i = 1; $i <= 5; $i++) {
    echo "Attempt $i:\n";
    
    $data = [
        'api_key' => $api_key,
        'secret_key' => $secret_key,
        'email' => $test_email,
        'password' => $wrong_password
    ];
    
    $ch = curl_init('http://localhost/trustnet/api/login.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    echo "  Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
    echo "  HTTP Code: $http_code\n\n";
    
    if (isset($result['status']) && $result['status'] == 'blocked') {
        echo "✅ BLOCKED! User is now blocked for 5 hours.\n";
        break;
    }
    
    sleep(1); // Small delay between attempts
}

echo "\n=== CHECK BLOCKED USERS ===\n";
$stmt = $pdo->prepare("SELECT * FROM api_user_blocks WHERE is_blocked = 1");
$stmt->execute();
$blocked = $stmt->fetchAll();

if ($blocked) {
    echo "Blocked users found:\n";
    foreach ($blocked as $b) {
        echo "  - {$b['user_email']} | Blocked until: {$b['blocked_until']} | Attempts: {$b['failed_attempts']}\n";
    }
} else {
    echo "No blocked users found.\n";
}

echo "\n=== CHECK FAILED ATTEMPTS ===\n";
$stmt = $pdo->prepare("SELECT * FROM failed_login_attempts ORDER BY attempt_time DESC LIMIT 10");
$stmt->execute();
$attempts = $stmt->fetchAll();

if ($attempts) {
    echo "Recent failed attempts:\n";
    foreach ($attempts as $a) {
        echo "  - {$a['user_identifier']} | IP: {$a['ip_address']} | Location: {$a['location']} | Time: {$a['attempt_time']}\n";
    }
} else {
    echo "No failed attempts found.\n";
}

echo "</pre>";
?><?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Blocking System</title>
    <style>
        body { font-family: monospace; background: #0A0F1C; color: #00D1FF; padding: 20px; }
        .attempt { background: #0F172A; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 3px solid #00D1FF; }
        .success { border-left-color: #00D1FF; }
        .error { border-left-color: #ffa502; }
        .blocked { border-left-color: #ff4757; background: rgba(255,71,87,0.1); }
        pre { background: #000; padding: 10px; overflow-x: auto; }
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 11px; }
        .status-error { background: rgba(255,165,2,0.2); color: #ffa502; }
        .status-blocked { background: rgba(255,71,87,0.2); color: #ff4757; }
        .status-success { background: rgba(0,209,255,0.2); color: #00D1FF; }
    </style>
</head>
<body>
    <h1>🔒 Testing 4 Failed Attempts = 5 Hours Block</h1>
";

// Get first website
$stmt = $pdo->query("SELECT id, website_name, api_key, secret_key FROM websites LIMIT 1");
$website = $stmt->fetch();

if (!$website) {
    die("<p style='color: red;'>No website found. Please create a website first.</p></body></html>");
}

// Get a test user from website_users
$stmt = $pdo->prepare("SELECT email FROM website_users WHERE website_id = ? LIMIT 1");
$stmt->execute([$website['id']]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p style='color: #ffa502;'>No test users found. <a href='setup-test-users.php'>Click here to create test users</a></p>";
    echo "</body></html>";
    exit();
}

$api_key = $website['api_key'];
$secret_key = $website['secret_key'];
$test_email = $user['email'];
$wrong_password = "wrongpassword123";

echo "<div style='background: #0F172A; padding: 15px; border-radius: 10px; margin-bottom: 20px;'>";
echo "<strong>📋 Test Configuration:</strong><br>";
echo "Website: {$website['website_name']}<br>";
echo "API Key: <code>$api_key</code><br>";
echo "Secret Key: <code>$secret_key</code><br>";
echo "Test Email: <strong>$test_email</strong><br>";
echo "Wrong Password: <code>$wrong_password</code><br>";
echo "</div>";

echo "<h2>Running 5 Login Attempts with Wrong Password...</h2>";

$results = [];

for ($i = 1; $i <= 5; $i++) {
    echo "<div class='attempt'>";
    echo "<strong>Attempt #$i</strong><br>";
    
    $data = [
        'api_key' => $api_key,
        'secret_key' => $secret_key,
        'email' => $test_email,
        'password' => $wrong_password
    ];
    
    $ch = curl_init('http://localhost/trustnet/api/login.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_error($ch)) {
        echo "  ❌ cURL Error: " . curl_error($ch) . "<br>";
    } else {
        $result = json_decode($response, true);
        
        if (isset($result['status'])) {
            $status_class = $result['status'];
            echo "  <span class='status-badge status-{$status_class}'>Status: {$result['status']}</span><br>";
            
            if ($result['status'] == 'error') {
                echo "  📝 Message: {$result['message']}<br>";
                if (isset($result['attempts_remaining'])) {
                    echo "  ⚠️ Attempts remaining before block: <strong>{$result['attempts_remaining']}</strong><br>";
                }
                if (isset($result['ip_address'])) {
                    echo "  🌐 IP Address: {$result['ip_address']}<br>";
                }
                if (isset($result['location'])) {
                    echo "  📍 Location: {$result['location']}<br>";
                }
            } elseif ($result['status'] == 'blocked') {
                echo "  🚫 Message: {$result['message']}<br>";
                echo "  ⏰ Blocked until: {$result['blocked_until']}<br>";
                echo "  📊 Failed attempts: {$result['failed_attempts']}<br>";
                echo "  ⏳ Remaining hours: {$result['remaining_hours']}<br>";
                echo "  🌐 IP Address: {$result['ip_address']}<br>";
            } elseif ($result['status'] == 'success') {
                echo "  ✅ Login successful!<br>";
                echo "  👤 User: {$result['user']['email']}<br>";
                echo "  🌐 IP: {$result['session']['ip']}<br>";
                echo "  📍 Location: {$result['session']['location']}<br>";
            }
        } else {
            echo "  Response: " . htmlspecialchars(substr($response, 0, 500)) . "<br>";
        }
    }
    
    curl_close($ch);
    echo "</div>";
    
    // Small delay between attempts
    if ($i < 5) {
        sleep(1);
    }
}

// Display database results
echo "<h2>📊 Database Status</h2>";

// Check api_user_blocks
$stmt = $pdo->prepare("SELECT * FROM api_user_blocks WHERE website_id = ? AND user_email = ?");
$stmt->execute([$website['id'], $test_email]);
$block_record = $stmt->fetch();

if ($block_record) {
    echo "<div class='attempt'>";
    echo "<strong>Block Record:</strong><br>";
    echo "User: {$block_record['user_email']}<br>";
    echo "IP: {$block_record['ip_address']}<br>";
    echo "Failed Attempts: {$block_record['failed_attempts']}<br>";
    echo "Is Blocked: " . ($block_record['is_blocked'] ? 'Yes' : 'No') . "<br>";
    if ($block_record['blocked_until']) {
        echo "Blocked Until: {$block_record['blocked_until']}<br>";
    }
    echo "</div>";
}

// Check failed attempts
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM failed_login_attempts WHERE website_id = ? AND user_identifier = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
$stmt->execute([$website['id'], $test_email]);
$failed_count = $stmt->fetch()['count'];
echo "<div class='attempt'>";
echo "<strong>Failed Attempts (Last Hour):</strong> $failed_count<br>";
echo "</div>";

// Check security events
$stmt = $pdo->prepare("SELECT * FROM security_events WHERE website_id = ? AND user_identifier = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$website['id'], $test_email]);
$events = $stmt->fetchAll();

if ($events) {
    echo "<div class='attempt'>";
    echo "<strong>Recent Security Events:</strong><br>";
    foreach ($events as $event) {
        echo "- [{$event['severity']}] {$event['event_type']}: {$event['event_details']}<br>";
    }
    echo "</div>";
}

echo "
<h2>🧪 Manual Testing with cURL</h2>
<p>Copy and run these commands in Command Prompt:</p>
<pre>
REM Replace with your actual keys
set API_KEY={$api_key}
set SECRET_KEY={$secret_key}

REM Attempt 1
curl -X POST http://localhost/trustnet/api/login.php -H \"Content-Type: application/json\" -d \"{\\\"api_key\\\":\\\"%API_KEY%\\\",\\\"secret_key\\\":\\\"%SECRET_KEY%\\\",\\\"email\\\":\\\"{$test_email}\\\",\\\"password\\\":\\\"wrong\\\"}\"

REM Check if blocked after 4 attempts
</pre>
";

echo "</body></html>";
?>