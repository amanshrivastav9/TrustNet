<?php
require_once 'includes/config.php';

echo "<h1>TrustNet Diagnostic</h1>";

// Check all required tables
$required_tables = [
    'websites',
    'website_users', 
    'api_user_blocks',
    'failed_login_attempts',
    'security_events',
    'login_logs',
    'activity_logs'
];

echo "<h2>Table Check:</h2>";
foreach ($required_tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "✅ $table exists ($count records)<br>";
        } else {
            echo "❌ $table does NOT exist<br>";
        }
    } catch (PDOException $e) {
        echo "❌ Error checking $table: " . $e->getMessage() . "<br>";
    }
}

// Check websites
echo "<h2>Websites:</h2>";
$websites = $pdo->query("SELECT * FROM websites")->fetchAll();
if ($websites) {
    foreach ($websites as $w) {
        echo "- ID: {$w['id']}, Name: {$w['website_name']}, API Key: {$w['api_key']}<br>";
    }
} else {
    echo "No websites found!<br>";
}

// Check website_users
echo "<h2>Website Users:</h2>";
$users = $pdo->query("SELECT * FROM website_users")->fetchAll();
if ($users) {
    foreach ($users as $u) {
        echo "- Email: {$u['email']}, Website ID: {$u['website_id']}<br>";
    }
} else {
    echo "No website users found! Run setup-test-users.php first.<br>";
}

// Check API endpoint
echo "<h2>API Test:</h2>";
if (!empty($websites) && !empty($users)) {
    $test_data = [
        'api_key' => $websites[0]['api_key'],
        'secret_key' => $websites[0]['secret_key'],
        'email' => $users[0]['email'],
        'password' => 'wrongpassword'
    ];
    
    $ch = curl_init('http://localhost/trustnet/api/login.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Code: $http_code<br>";
    echo "Response: " . htmlspecialchars($response) . "<br>";
}
?>