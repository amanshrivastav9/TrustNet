<?php
/**
 * Webhook Handler for external integrations
 */

header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/functions.php';

$api_key = $_GET['api_key'] ?? $_SERVER['HTTP_X_API_KEY'] ?? '';

if (empty($api_key)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'API key required']);
    exit();
}

// Verify API key
$stmt = $pdo->prepare("SELECT id, user_id FROM websites WHERE api_key = ? AND status = 'active'");
$stmt->execute([$api_key]);
$website = $stmt->fetch();

if (!$website) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
    exit();
}

$webhook_type = $_GET['type'] ?? $_POST['type'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

switch ($webhook_type) {
    case 'user_registered':
        // Handle user registration webhook
        $user_email = $input['email'] ?? '';
        $user_name = $input['name'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'webhook_user_registered', ?, ?, NOW())");
        $stmt->execute([$website['id'], json_encode($input), getUserIP()]);
        
        echo json_encode(['status' => 'success', 'message' => 'Webhook processed']);
        break;
        
    case 'user_login':
        // Handle user login webhook
        $user_email = $input['email'] ?? '';
        $login_status = $input['status'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'webhook_user_login', ?, ?, NOW())");
        $stmt->execute([$website['id'], json_encode($input), getUserIP()]);
        
        echo json_encode(['status' => 'success', 'message' => 'Webhook processed']);
        break;
        
    case 'security_alert':
        // Handle security alert webhook
        $alert_type = $input['type'] ?? '';
        $alert_message = $input['message'] ?? '';
        
        // Log security alert
        $stmt = $pdo->prepare("INSERT INTO activity_logs (website_id, activity_type, activity_details, ip_address, timestamp) 
                               VALUES (?, 'security_alert', ?, ?, NOW())");
        $stmt->execute([$website['id'], json_encode($input), getUserIP()]);
        
        // Send email notification if needed
        if ($input['notify_admin'] ?? false) {
            sendSecurityAlertEmail($pdo, $website['user_id'], $alert_type, $alert_message);
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Alert logged']);
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Unknown webhook type']);
        break;
}

function sendSecurityAlertEmail($pdo, $user_id, $alert_type, $message) {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if ($user) {
        $subject = "Security Alert: {$alert_type}";
        $body = "A security alert has been triggered:\n\nType: {$alert_type}\nMessage: {$message}\nTime: " . date('Y-m-d H:i:s');
        mail($user['email'], $subject, $body, "From: security@trustnet.com");
    }
}
?>