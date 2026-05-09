<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Test with different IPs
$test_ips = [
    '8.8.8.8',      // Google DNS - USA
    '1.1.1.1',      // Cloudflare - USA
    '31.13.79.246', // Facebook - Ireland/USA
    '151.101.1.69'  // Fastly - multiple locations
];

echo "<!DOCTYPE html>
<html>
<head>
    <title>Geo Location Test - TrustNet</title>
    <style>
        body { font-family: monospace; background: #0A0F1C; color: #00D1FF; padding: 20px; }
        .result { background: #0F172A; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 3px solid #00D1FF; }
        .ip { color: #ffa502; font-weight: bold; }
        .label { color: #7B61FF; }
    </style>
</head>
<body>
    <h1>IP Geolocation Test (ip-api.com)</h1>
    <h3>Your IP: " . getUserIP() . "</h3>
    <hr>";

foreach ($test_ips as $test_ip) {
    echo "<div class='result'>";
    echo "<div class='ip'>Testing IP: {$test_ip}</div>";
    $location = getLocationFromIP($test_ip);
    
    if ($location['status'] == 'success') {
        echo "<div><span class='label'>Country:</span> " . getCountryFlag($location['country_code']) . " {$location['country']} ({$location['country_code']})</div>";
        echo "<div><span class='label'>Region:</span> {$location['region']}</div>";
        echo "<div><span class='label'>City:</span> {$location['city']}</div>";
        echo "<div><span class='label'>Coordinates:</span> {$location['lat']}, {$location['lon']}</div>";
        echo "<div><span class='label'>ISP:</span> {$location['isp']}</div>";
        echo "<div><span class='label'>Organization:</span> {$location['org']}</div>";
        echo "<div><span class='label'>AS:</span> {$location['as']}</div>";
    } else {
        echo "<div style='color: #ff4757;'>Failed to get location data</div>";
    }
    echo "</div>";
}

// Test risk score calculation
echo "<h2>Risk Score Test</h2>";
$test_cases = [
    ['ip' => '8.8.8.8', 'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'attempts' => 0],
    ['ip' => '8.8.8.8', 'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'attempts' => 3],
    ['ip' => '1.1.1.1', 'ua' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'attempts' => 0],
];

foreach ($test_cases as $test) {
    $score = calculateRiskScore($test['ip'], $test['ua'], $test['attempts']);
    $level = getRiskLevel($score);
    echo "<div class='result'>";
    echo "<div>IP: {$test['ip']} | User Agent: " . substr($test['ua'], 0, 50) . "... | Failed Attempts: {$test['attempts']}</div>";
    echo "<div style='color: {$level['color']};'>Risk Score: {$score} - {$level['text']} Risk</div>";
    echo "</div>";
}

echo "</body></html>";
?>