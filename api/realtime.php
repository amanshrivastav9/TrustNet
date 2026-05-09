<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Allow CORS for real-time updates
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

$action = $_GET['action'] ?? '';
$api_key = $_GET['api_key'] ?? '';

if (empty($api_key)) {
    echo json_encode(['status' => 'error', 'message' => 'API key required']);
    exit();
}

// Verify website
$stmt = $pdo->prepare("SELECT id FROM websites WHERE api_key = ? AND status = 'active'");
$stmt->execute([$api_key]);
$website = $stmt->fetch();

if (!$website) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
    exit();
}

$website_id = $website['id'];
$ip_address = getUserIP();
$session_id = $_GET['session_id'] ?? bin2hex(random_bytes(16));

switch ($action) {
    case 'update':
        // Update real-time visitor
        $current_page = $_GET['page'] ?? $_SERVER['HTTP_REFERER'] ?? '/';
        
        // Clean up old visitors (inactive for more than 5 minutes)
        $stmt = $pdo->prepare("DELETE FROM realtime_visitors WHERE last_activity < DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
        $stmt->execute();
        
        // Update or insert current visitor
        $stmt = $pdo->prepare("INSERT INTO realtime_visitors (website_id, session_id, ip_address, current_page, last_activity, first_seen) 
                               VALUES (?, ?, ?, ?, NOW(), NOW()) 
                               ON DUPLICATE KEY UPDATE 
                               current_page = ?, last_activity = NOW()");
        $stmt->execute([$website_id, $session_id, $ip_address, $current_page, $current_page]);
        
        echo json_encode(['status' => 'success', 'message' => 'Real-time data updated']);
        break;
        
    case 'get':
        // Get real-time visitor count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM realtime_visitors 
                               WHERE website_id = ? AND last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
        $stmt->execute([$website_id]);
        $count = $stmt->fetch()['count'];
        
        // Get visitor details
        $stmt = $pdo->prepare("SELECT ip_address, current_page, last_activity, first_seen 
                               FROM realtime_visitors 
                               WHERE website_id = ? AND last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                               ORDER BY last_activity DESC");
        $stmt->execute([$website_id]);
        $visitors = $stmt->fetchAll();
        
        foreach ($visitors as &$visitor) {
            $location = getLocationFromIP($visitor['ip_address']);
            $visitor['location'] = $location['status'] == 'success' ? 
                "{$location['city']}, {$location['country']}" : 'Unknown';
            $visitor['country_flag'] = getCountryFlag($location['country_code'] ?? '');
        }
        
        echo json_encode([
            'status' => 'success',
            'count' => $count,
            'visitors' => $visitors,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        break;
        
    case 'cleanup':
        // Manual cleanup
        $stmt = $pdo->prepare("DELETE FROM realtime_visitors WHERE last_activity < DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
        $stmt->execute();
        echo json_encode(['status' => 'success', 'deleted' => $stmt->rowCount()]);
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>