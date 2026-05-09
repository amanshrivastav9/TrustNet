<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../includes/config.php';

// Simple function to get IP
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
    exit();
}

// Validate required fields
if (empty($input['api_key']) || empty($input['secret_key']) || empty($input['email']) || empty($input['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit();
}

$api_key = $input['api_key'];
$secret_key = $input['secret_key'];
$email = $input['email'];
$password = $input['password'];
$ip_address = getUserIP();

try {
    // 1. VERIFY WEBSITE
    $stmt = $pdo->prepare("SELECT id, status FROM websites WHERE api_key = ? AND secret_key = ?");
    $stmt->execute([$api_key, $secret_key]);
    $website = $stmt->fetch();
    
    if (!$website) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid API credentials']);
        exit();
    }
    
    if ($website['status'] != 'active') {
        echo json_encode(['status' => 'error', 'message' => 'Website is inactive']);
        exit();
    }
    
    $website_id = $website['id'];
    
    // 2. CHECK IF USER IS BLOCKED
    $stmt = $pdo->prepare("SELECT * FROM api_user_blocks 
                           WHERE website_id = ? AND user_email = ? 
                           AND is_blocked = 1 AND blocked_until > NOW()");
    $stmt->execute([$website_id, $email]);
    $blocked = $stmt->fetch();
    
    if ($blocked) {
        $remaining_hours = ceil((strtotime($blocked['blocked_until']) - time()) / 3600);
        echo json_encode([
            'status' => 'blocked',
            'message' => 'Account is blocked due to multiple failed login attempts',
            'blocked_until' => $blocked['blocked_until'],
            'failed_attempts' => $blocked['failed_attempts'],
            'remaining_hours' => $remaining_hours
        ]);
        exit();
    }
    
    // 3. GET USER
    $stmt = $pdo->prepare("SELECT * FROM website_users WHERE website_id = ? AND email = ?");
    $stmt->execute([$website_id, $email]);
    $user = $stmt->fetch();
    
    // 4. VERIFY PASSWORD
    $password_valid = false;
    if ($user) {
        $password_valid = password_verify($password, $user['password']);
    }
    
    if ($password_valid) {
        // SUCCESS - Reset attempts
        $stmt = $pdo->prepare("DELETE FROM api_user_blocks WHERE website_id = ? AND user_email = ?");
        $stmt->execute([$website_id, $email]);
        
        $stmt = $pdo->prepare("DELETE FROM failed_login_attempts WHERE website_id = ? AND user_identifier = ?");
        $stmt->execute([$website_id, $email]);
        
        // Update last login
        $stmt = $pdo->prepare("UPDATE website_users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name']
            ]
        ]);
    } else {
        // FAILED - Track attempts
        
        // Count recent failed attempts
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM failed_login_attempts 
                               WHERE website_id = ? AND user_identifier = ? 
                               AND attempt_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        $stmt->execute([$website_id, $email]);
        $attempt_count = $stmt->fetch()['count'];
        
        $new_attempt_count = $attempt_count + 1;
        
        // Log this failed attempt
        $stmt = $pdo->prepare("INSERT INTO failed_login_attempts (website_id, user_identifier, ip_address, attempt_time) 
                               VALUES (?, ?, ?, NOW())");
        $stmt->execute([$website_id, $email, $ip_address]);
        
        // Check if should block
        if ($new_attempt_count >= 4) {
            // BLOCK USER FOR 5 HOURS
            $blocked_until = date('Y-m-d H:i:s', strtotime('+5 hours'));
            
            // Check if record exists
            $stmt = $pdo->prepare("SELECT id FROM api_user_blocks WHERE website_id = ? AND user_email = ?");
            $stmt->execute([$website_id, $email]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                $stmt = $pdo->prepare("UPDATE api_user_blocks SET failed_attempts = ?, is_blocked = 1, blocked_until = ? 
                                       WHERE website_id = ? AND user_email = ?");
                $stmt->execute([$new_attempt_count, $blocked_until, $website_id, $email]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO api_user_blocks (website_id, user_email, ip_address, failed_attempts, is_blocked, blocked_until) 
                                       VALUES (?, ?, ?, ?, 1, ?)");
                $stmt->execute([$website_id, $email, $ip_address, $new_attempt_count, $blocked_until]);
            }
            
            echo json_encode([
                'status' => 'blocked',
                'message' => 'Account blocked for 5 hours due to multiple failed attempts',
                'blocked_until' => $blocked_until,
                'failed_attempts' => $new_attempt_count,
                'attempts_remaining' => 0
            ]);
        } else {
            $attempts_remaining = 4 - $new_attempt_count;
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email or password',
                'attempts_used' => $new_attempt_count,
                'attempts_remaining' => $attempts_remaining,
                'will_block_after' => $attempts_remaining
            ]);
        }
    }
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>