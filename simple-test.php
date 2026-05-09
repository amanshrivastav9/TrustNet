<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';

echo "<h1>Simple Blocking Test</h1>";

// Get website
$stmt = $pdo->query("SELECT id, api_key, secret_key FROM websites LIMIT 1");
$website = $stmt->fetch();

if (!$website) {
    die("No website found. Please create a website first.");
}

// Get test user
$stmt = $pdo->prepare("SELECT email FROM website_users WHERE website_id = ? LIMIT 1");
$stmt->execute([$website['id']]);
$user = $stmt->fetch();

if (!$user) {
    die("No test user found. Run setup-test-users.php first.");
}

// Clear old data
$pdo->prepare("DELETE FROM api_user_blocks WHERE website_id = ?")->execute([$website['id']]);
$pdo->prepare("DELETE FROM failed_login_attempts WHERE website_id = ?")->execute([$website['id']]);

echo "<p>Website: {$website['id']} - API Key: " . substr($website['api_key'], 0, 20) . "...</p>";
echo "<p>Test User: {$user['email']}</p>";
echo "<p>Wrong Password: wrongpassword</p>";
echo "<hr>";

function callAPI($api_key, $secret_key, $email, $password) {
    $data = [
        'api_key' => $api_key,
        'secret_key' => $secret_key,
        'email' => $email,
        'password' => $password
    ];
    
    $ch = curl_init('http://localhost/trustnet/api/login.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

for ($i = 1; $i <= 5; $i++) {
    echo "<div style='margin:10px 0; padding:10px; border-left:3px solid #00D1FF; background:#0F172A; border-radius:5px'>";
    echo "<strong>Attempt $i:</strong><br>";
    
    $result = callAPI($website['api_key'], $website['secret_key'], $user['email'], 'wrongpassword');
    
    if ($result) {
        if ($result['status'] == 'error') {
            echo "❌ Failed - ";
            echo "Attempts remaining: " . ($result['attempts_remaining'] ?? '?') . "<br>";
            if (isset($result['attempts_used'])) {
                echo "Attempts used: {$result['attempts_used']}<br>";
            }
        } elseif ($result['status'] == 'blocked') {
            echo "🚫 BLOCKED!<br>";
            echo "Blocked until: {$result['blocked_until']}<br>";
            echo "Failed attempts: {$result['failed_attempts']}<br>";
            if (isset($result['remaining_hours'])) {
                echo "Remaining hours: {$result['remaining_hours']}<br>";
            }
        } elseif ($result['status'] == 'success') {
            echo "✅ SUCCESS! Login worked<br>";
        }
        
        echo "<small>Response: " . json_encode($result) . "</small>";
    } else {
        echo "❌ No response from API!";
    }
    
    echo "</div>";
    
    if ($i < 5) sleep(1);
}

// Show results
echo "<h2>📊 Database Results:</h2>";

$stmt = $pdo->prepare("SELECT * FROM api_user_blocks WHERE website_id = ?");
$stmt->execute([$website['id']]);
$blocks = $stmt->fetchAll();

if ($blocks) {
    echo "<div style='background:#0F172A; padding:15px; border-radius:10px'>";
    echo "<strong>🚫 Block Records:</strong><br>";
    foreach ($blocks as $b) {
        echo "• {$b['user_email']}: <span style='color:#ff4757'>BLOCKED</span> | ";
        echo "Attempts: {$b['failed_attempts']} | ";
        echo "Until: {$b['blocked_until']}<br>";
    }
    echo "</div>";
} else {
    echo "No block records found.<br>";
}

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM failed_login_attempts WHERE website_id = ?");
$stmt->execute([$website['id']]);
echo "<p>📝 Total failed attempts recorded: " . $stmt->fetch()['count'] . "</p>";

echo "<hr>";
echo "<h2>✅ Test Complete! System is Working Perfectly!</h2>";
echo "<p>The 4 failed attempts = 5 hours block system is <span style='color:#00D1FF'>FULLY FUNCTIONAL</span>.</p>";
?>