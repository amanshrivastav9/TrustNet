<?php
require_once 'includes/config.php';

echo "<h1>Quick Test for Blocking System</h1>";

// Get website
$stmt = $pdo->query("SELECT id, api_key, secret_key FROM websites LIMIT 1");
$website = $stmt->fetch();

if (!$website) {
    die("No website found");
}

// Get test user
$stmt = $pdo->prepare("SELECT email FROM website_users WHERE website_id = ? LIMIT 1");
$stmt->execute([$website['id']]);
$user = $stmt->fetch();

if (!$user) {
    die("No test user found. Run setup-test-users.php first.");
}

echo "<div style='background:#0F172A; padding:15px; border-radius:10px; margin-bottom:20px'>";
echo "<strong>Testing with:</strong><br>";
echo "API Key: <code>{$website['api_key']}</code><br>";
echo "Email: <code>{$user['email']}</code><br>";
echo "Password: <code>wrongpassword</code><br>";
echo "</div>";

// Clear previous blocks
$pdo->prepare("DELETE FROM api_user_blocks WHERE website_id = ?")->execute([$website['id']]);
$pdo->prepare("DELETE FROM failed_login_attempts WHERE website_id = ?")->execute([$website['id']]);
echo "<p>✓ Cleared previous blocks and attempts</p>";

echo "<h2>Running 5 Failed Attempts:</h2>";

for ($i = 1; $i <= 5; $i++) {
    $data = [
        'api_key' => $website['api_key'],
        'secret_key' => $website['secret_key'],
        'email' => $user['email'],
        'password' => 'wrongpassword'
    ];
    
    $ch = curl_init('http://localhost/trustnet/api/login.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    echo "<div style='border-left:3px solid ";
    if ($result['status'] == 'success') echo "#00D1FF";
    elseif ($result['status'] == 'blocked') echo "#ff4757";
    else echo "#ffa502";
    echo "; padding:10px; margin:10px 0; background:#0F172A; border-radius:5px'>";
    
    echo "<strong>Attempt $i:</strong> ";
    
    if ($result['status'] == 'error') {
        echo "❌ Failed - " . ($result['attempts_remaining'] ?? '?') . " attempts remaining<br>";
        echo "<small>IP: {$result['ip_address']} | Location: {$result['location']}</small>";
    } elseif ($result['status'] == 'blocked') {
        echo "🚫 BLOCKED! - " . ($result['remaining_hours'] ?? 5) . " hours block<br>";
        echo "<small>Blocked until: {$result['blocked_until']}</small>";
    } elseif ($result['status'] == 'success') {
        echo "✅ SUCCESS! Login worked<br>";
    }
    
    echo "</div>";
    
    sleep(1);
}

// Show database status
echo "<h2>Database Status:</h2>";

$blocked = $pdo->prepare("SELECT * FROM api_user_blocks WHERE website_id = ?");
$blocked->execute([$website['id']]);
$blocks = $blocked->fetchAll();

if ($blocks) {
    echo "<div style='background:#0F172A; padding:15px; border-radius:10px'>";
    echo "<strong>Blocked Records:</strong><br>";
    foreach ($blocks as $b) {
        echo "- {$b['user_email']} | Blocked: " . ($b['is_blocked'] ? 'Yes' : 'No') . " | Until: {$b['blocked_until']}<br>";
    }
    echo "</div>";
}

$attempts = $pdo->prepare("SELECT COUNT(*) as count FROM failed_login_attempts WHERE website_id = ?");
$attempts->execute([$website['id']]);
echo "<p>Total failed attempts recorded: " . $attempts->fetch()['count'] . "</p>";

echo "<h3>✅ Test Complete!</h3>";
echo "<p>If you saw BLOCKED on attempt 4 or 5, the system is working correctly.</p>";
?>