<?php
require_once 'includes/config.php';

echo "<h1>Debugging API</h1>";

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
    die("No test user found");
}

echo "<h2>Testing API with curl</h2>";

$data = [
    'api_key' => $website['api_key'],
    'secret_key' => $website['secret_key'],
    'email' => $user['email'],
    'password' => 'wrongpassword'
];

echo "<pre>";
echo "Request Data:\n";
print_r($data);
echo "</pre>";

$ch = curl_init('http://localhost/trustnet/api/login.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$error = curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "<h3>Response:</h3>";
echo "<pre>";
echo "HTTP Code: " . $info['http_code'] . "\n";
if ($error) {
    echo "CURL Error: $error\n";
}
echo "Response: " . htmlspecialchars($response) . "\n";
echo "</pre>";

if ($response) {
    $json = json_decode($response, true);
    if ($json) {
        echo "<h3>Decoded Response:</h3>";
        echo "<pre>";
        print_r($json);
        echo "</pre>";
    } else {
        echo "<h3 style='color:red'>JSON Parse Error! Response is not valid JSON</h3>";
    }
}
?>