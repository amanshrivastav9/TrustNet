<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/functions.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit();
}

$api_key = $input['api_key'] ?? '';
$action = $input['action'] ?? '';

if (empty($api_key)) {
    echo json_encode(['status' => 'error', 'message' => 'API key required']);
    exit();
}

// Verify API key
$stmt = $pdo->prepare("SELECT id, user_id, website_name FROM websites WHERE api_key = ? AND status = 'active'");
$stmt->execute([$api_key]);
$website = $stmt->fetch();

if (!$website) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
    exit();
}

$website_id = $website['id'];
$ip_address = getUserIP();

// Check IP blacklist
if (isIPBlacklisted($pdo, $ip_address)) {
    echo json_encode(['status' => 'error', 'message' => 'IP blocked']);
    exit();
}

switch ($action) {
    case 'pageview':
        $page_url = $input['page_url'] ?? '';
        $referrer = $input['referrer'] ?? '';
        $user_agent = $input['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'];
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'pageview', ?, ?, NOW())");
        $stmt->execute([$website_id, "Page viewed: {$page_url} | Referrer: {$referrer} | UA: {$user_agent}", $ip_address]);
        
        echo json_encode(['status' => 'success', 'message' => 'Pageview tracked']);
        break;
        
    case 'click':
        $element = $input['element'] ?? '';
        $page_url = $input['page_url'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'click', ?, ?, NOW())");
        $stmt->execute([$website_id, "Click on {$element} at {$page_url}", $ip_address]);
        
        echo json_encode(['status' => 'success', 'message' => 'Click tracked']);
        break;
        
    case 'session':
        $session_duration = $input['duration'] ?? 0;
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'session', ?, ?, NOW())");
        $stmt->execute([$website_id, "Session duration: {$session_duration} seconds", $ip_address]);
        
        echo json_encode(['status' => 'success', 'message' => 'Session tracked']);
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>