<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Get input data
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

// Verify website
$stmt = $pdo->prepare("SELECT id, status FROM websites WHERE api_key = ?");
$stmt->execute([$api_key]);
$website = $stmt->fetch();

if (!$website) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
    exit();
}

if ($website['status'] != 'active') {
    echo json_encode(['status' => 'error', 'message' => 'Website inactive']);
    exit();
}

$website_id = $website['id'];
$ip_address = getUserIP();

// Check IP blacklist
if (isIPBlacklisted($pdo, $ip_address)) {
    echo json_encode(['status' => 'error', 'message' => 'IP blocked']);
    exit();
}

// Process different activity types
switch ($action) {
    case 'pageview':
        $page_url = $input['page_url'] ?? '';
        $page_title = $input['page_title'] ?? '';
        $referrer = $input['referrer'] ?? '';
        $viewport = $input['viewport'] ?? '';
        
        // Get location data
        $location = getLocationFromIP($ip_address);
        $location_str = $location['status'] == 'success' ? 
            "{$location['city']}, {$location['region']}, {$location['country']}" : 'Unknown';
        
        $details = json_encode([
            'page_url' => $page_url,
            'page_title' => $page_title,
            'referrer' => $referrer,
            'viewport' => $viewport,
            'location' => $location_str
        ]);
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'pageview', ?, ?, NOW())");
        $stmt->execute([$website_id, $details, $ip_address]);
        
        // Update session duration tracking
        updateUserSession($pdo, $website_id, $ip_address, 'pageview');
        
        // Calculate risk score
        $failed_attempts = getFailedAttempts($pdo, $website_id, $ip_address);
        $risk_score = calculateRiskScore($ip_address, $_SERVER['HTTP_USER_AGENT'] ?? '', $failed_attempts);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Pageview tracked',
            'risk_score' => $risk_score
        ]);
        break;
        
    case 'click':
        $element = $input['element'] ?? '';
        $element_text = $input['element_text'] ?? '';
        $page_url = $input['page_url'] ?? '';
        $coordinates = $input['coordinates'] ?? [];
        
        $details = json_encode([
            'element' => $element,
            'element_text' => substr($element_text, 0, 100),
            'page_url' => $page_url,
            'coordinates' => $coordinates
        ]);
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'click', ?, ?, NOW())");
        $stmt->execute([$website_id, $details, $ip_address]);
        
        echo json_encode(['status' => 'success', 'message' => 'Click tracked']);
        break;
        
    case 'scroll':
        $depth = $input['depth'] ?? 0;
        $page_url = $input['page_url'] ?? '';
        
        $details = json_encode([
            'depth' => $depth,
            'page_url' => $page_url
        ]);
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'scroll', ?, ?, NOW())");
        $stmt->execute([$website_id, $details, $ip_address]);
        
        echo json_encode(['status' => 'success', 'message' => 'Scroll tracked']);
        break;
        
    case 'session':
        $duration = $input['duration'] ?? 0;
        $page_url = $input['page_url'] ?? '';
        $pages_visited = $input['pages_visited'] ?? 1;
        
        $details = json_encode([
            'duration' => $duration,
            'page_url' => $page_url,
            'pages_visited' => $pages_visited
        ]);
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'session_end', ?, ?, NOW())");
        $stmt->execute([$website_id, $details, $ip_address]);
        
        // Update session in database
        updateUserSession($pdo, $website_id, $ip_address, 'end', $duration);
        
        echo json_encode(['status' => 'success', 'message' => 'Session tracked']);
        break;
        
    case 'form_submit':
        $form_id = $input['form_id'] ?? '';
        $form_action = $input['form_action'] ?? '';
        $fields = $input['fields'] ?? [];
        
        $details = json_encode([
            'form_id' => $form_id,
            'form_action' => $form_action,
            'fields' => $fields
        ]);
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'form_submit', ?, ?, NOW())");
        $stmt->execute([$website_id, $details, $ip_address]);
        
        echo json_encode(['status' => 'success', 'message' => 'Form submission tracked']);
        break;
        
    case 'time_on_page':
        $seconds = $input['seconds'] ?? 0;
        $page_url = $input['page_url'] ?? '';
        
        // Update or create time tracking
        $stmt = $pdo->prepare("INSERT INTO page_time_tracking (website_id, ip_address, page_url, total_time, last_update) 
                               VALUES (?, ?, ?, ?, NOW()) 
                               ON DUPLICATE KEY UPDATE 
                               total_time = total_time + ?, last_update = NOW()");
        $stmt->execute([$website_id, $ip_address, $page_url, $seconds, $seconds]);
        
        echo json_encode(['status' => 'success', 'message' => 'Time tracked']);
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

// Helper function to update user session
function updateUserSession($pdo, $website_id, $ip_address, $action, $duration = 0) {
    if ($action == 'pageview') {
        // Check if there's an active session
        $stmt = $pdo->prepare("SELECT id, session_start, page_count FROM active_sessions 
                               WHERE website_id = ? AND ip_address = ? AND session_end IS NULL");
        $stmt->execute([$website_id, $ip_address]);
        $session = $stmt->fetch();
        
        if ($session) {
            // Update existing session
            $stmt = $pdo->prepare("UPDATE active_sessions SET page_count = page_count + 1, last_activity = NOW() 
                                   WHERE id = ?");
            $stmt->execute([$session['id']]);
        } else {
            // Create new session
            $session_id = bin2hex(random_bytes(16));
            $stmt = $pdo->prepare("INSERT INTO active_sessions (website_id, session_id, ip_address, session_start, last_activity, page_count) 
                                   VALUES (?, ?, ?, NOW(), NOW(), 1)");
            $stmt->execute([$website_id, $session_id, $ip_address]);
        }
    } elseif ($action == 'end' && $duration > 0) {
        // End session
        $stmt = $pdo->prepare("UPDATE active_sessions SET session_end = NOW(), total_duration = ? 
                               WHERE website_id = ? AND ip_address = ? AND session_end IS NULL");
        $stmt->execute([$duration, $website_id, $ip_address]);
    }
}

// Helper function to get failed attempts
function getFailedAttempts($pdo, $website_id, $ip_address) {
    $stmt = $pdo->prepare("SELECT failed_attempts FROM api_user_blocks 
                           WHERE website_id = ? AND ip_address = ? 
                           ORDER BY id DESC LIMIT 1");
    $stmt->execute([$website_id, $ip_address]);
    $result = $stmt->fetch();
    return $result ? $result['failed_attempts'] : 0;
}
?>