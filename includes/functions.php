<?php
require_once 'config.php';

// Generate API Key
function generateApiKey() {
    return 'TRN_' . bin2hex(random_bytes(16));
}

// Generate Secret Key
function generateSecretKey() {
    return 'SK_' . bin2hex(random_bytes(24));
}

// Get user IP address
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        return $_SERVER['HTTP_X_REAL_IP'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

// Get location from IP
function getLocationFromIP($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return ['country' => 'Local Network', 'city' => 'Local', 'status' => 'success'];
    }
    
    $url = "http://ip-api.com/json/{$ip}?fields=status,country,city,regionName";
    $response = @file_get_contents($url);
    if ($response) {
        $data = json_decode($response, true);
        if ($data && $data['status'] == 'success') {
            return $data;
        }
    }
    return ['country' => 'Unknown', 'city' => 'Unknown', 'status' => 'error'];
}

// Get location string
function getLocationString($ip) {
    $location = getLocationFromIP($ip);
    if ($location['status'] == 'success') {
        return "{$location['city']}, {$location['regionName']}, {$location['country']}";
    }
    return 'Unknown Location';
}

// Get browser
function getBrowser() {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (stripos($ua, 'Chrome') !== false && stripos($ua, 'Edg') === false) return 'Chrome';
    if (stripos($ua, 'Firefox') !== false) return 'Firefox';
    if (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) return 'Safari';
    if (stripos($ua, 'Edg') !== false) return 'Edge';
    if (stripos($ua, 'Opera') !== false) return 'Opera';
    return 'Unknown';
}

// Get device
function getDevice() {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (stripos($ua, 'iPad') !== false || stripos($ua, 'Tablet') !== false) return 'Tablet';
    if (stripos($ua, 'Mobile') !== false || stripos($ua, 'Android') !== false || stripos($ua, 'iPhone') !== false) return 'Mobile';
    return 'Desktop';
}

// Get OS
function getOS() {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (stripos($ua, 'Windows') !== false) return 'Windows';
    if (stripos($ua, 'Mac OS') !== false || stripos($ua, 'Macintosh') !== false) return 'MacOS';
    if (stripos($ua, 'Linux') !== false) return 'Linux';
    if (stripos($ua, 'Android') !== false) return 'Android';
    if (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) return 'iOS';
    return 'Unknown';
}

// Generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Check if user is blocked
function isUserBlocked($pdo, $website_id, $user_email) {
    $stmt = $pdo->prepare("SELECT * FROM api_user_blocks 
                           WHERE website_id = ? AND user_email = ? 
                           AND is_blocked = 1 AND blocked_until > NOW()");
    $stmt->execute([$website_id, $user_email]);
    return $stmt->fetch();
}

// Log failed attempt
function logFailedAttempt($pdo, $website_id, $user_email, $ip_address) {
    // Get recent failed attempts
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM failed_login_attempts 
                           WHERE website_id = ? AND user_identifier = ? 
                           AND attempt_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->execute([$website_id, $user_email]);
    $attempt_count = $stmt->fetch()['count'];
    
    $new_attempt_count = $attempt_count + 1;
    
    // Log this attempt
    $stmt = $pdo->prepare("INSERT INTO failed_login_attempts (website_id, user_identifier, ip_address, attempt_time) 
                           VALUES (?, ?, ?, NOW())");
    $stmt->execute([$website_id, $user_email, $ip_address]);
    
    // Check if should block
    if ($new_attempt_count >= 4) {
        $blocked_until = date('Y-m-d H:i:s', strtotime('+5 hours'));
        
        $stmt = $pdo->prepare("SELECT id FROM api_user_blocks WHERE website_id = ? AND user_email = ?");
        $stmt->execute([$website_id, $user_email]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            $stmt = $pdo->prepare("UPDATE api_user_blocks SET failed_attempts = ?, is_blocked = 1, blocked_until = ? 
                                   WHERE website_id = ? AND user_email = ?");
            $stmt->execute([$new_attempt_count, $blocked_until, $website_id, $user_email]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO api_user_blocks (website_id, user_email, ip_address, failed_attempts, is_blocked, blocked_until) 
                                   VALUES (?, ?, ?, ?, 1, ?)");
            $stmt->execute([$website_id, $user_email, $ip_address, $new_attempt_count, $blocked_until]);
        }
        
        return ['blocked' => true, 'blocked_until' => $blocked_until, 'attempts' => $new_attempt_count];
    }
    
    return ['blocked' => false, 'attempts' => $new_attempt_count, 'attempts_remaining' => 4 - $new_attempt_count];
}

// Reset failed attempts
function resetFailedAttempts($pdo, $website_id, $user_email) {
    $stmt = $pdo->prepare("DELETE FROM api_user_blocks WHERE website_id = ? AND user_email = ?");
    $stmt->execute([$website_id, $user_email]);
    
    $stmt = $pdo->prepare("DELETE FROM failed_login_attempts WHERE website_id = ? AND user_identifier = ?");
    $stmt->execute([$website_id, $user_email]);
}

// Rate limiting
function checkRateLimit($pdo, $api_key) {
    $ip = getUserIP();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM api_requests 
                           WHERE api_key = ? AND request_ip = ? 
                           AND request_time > DATE_SUB(NOW(), INTERVAL 1 MINUTE)");
    $stmt->execute([$api_key, $ip]);
    $result = $stmt->fetch();
    return $result['count'] < 100;
}

// Log API request
function logAPIRequest($pdo, $website_id, $api_key, $endpoint, $status_code, $response_time) {
    $stmt = $pdo->prepare("INSERT INTO api_requests (website_id, api_key, endpoint, request_ip, status_code, response_time, request_time) 
                           VALUES (?, ?, ?, ?, ?, ?, NOW())");
    return $stmt->execute([$website_id, $api_key, $endpoint, getUserIP(), $status_code, $response_time]);
}

// Check IP blacklist
function isIPBlacklisted($pdo, $ip) {
    $stmt = $pdo->prepare("SELECT * FROM ip_blacklist WHERE ip_address = ?");
    $stmt->execute([$ip]);
    return $stmt->fetch();
}

// Get website analytics
function getWebsiteAnalytics($pdo, $website_id, $user_id, $is_admin = false) {
    if (!$is_admin) {
        $stmt = $pdo->prepare("SELECT id FROM websites WHERE id = ? AND user_id = ?");
        $stmt->execute([$website_id, $user_id]);
        if (!$stmt->fetch()) return null;
    }
    
    $stats = [];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM login_logs WHERE website_id = ?");
    $stmt->execute([$website_id]);
    $stats['total_visits'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_identifier) as unique_visitors FROM login_logs WHERE website_id = ?");
    $stmt->execute([$website_id]);
    $stats['unique_visitors'] = $stmt->fetch()['unique_visitors'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM activity_logs WHERE website_id = ? AND activity_type = 'pageview'");
    $stmt->execute([$website_id]);
    $stats['pageviews'] = $stmt->fetch()['total'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM api_user_blocks WHERE website_id = ? AND is_blocked = 1");
    $stmt->execute([$website_id]);
    $stats['blocked_users'] = $stmt->fetch()['total'];
    
    return $stats;
}

// Get user websites
function getUserWebsites($pdo, $user_id, $is_admin = false) {
    if ($is_admin) {
        $stmt = $pdo->query("SELECT w.*, u.name as owner_name FROM websites w LEFT JOIN users u ON w.user_id = u.id ORDER BY w.created_at DESC");
        return $stmt->fetchAll();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM websites WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
}

// Calculate risk score
function calculateRiskScore($ip, $user_agent, $failed_attempts = 0) {
    $score = 10;
    if ($failed_attempts > 0) {
        $score += min($failed_attempts * 10, 50);
    }
    if (stripos($user_agent, 'bot') !== false || stripos($user_agent, 'crawl') !== false) {
        $score += 70;
    }
    if (empty($user_agent) || strlen($user_agent) < 10) {
        $score += 20;
    }
    return min($score, 100);
}

// Get risk level
function getRiskLevel($score) {
    if ($score <= 30) return ['text' => 'Low', 'color' => '#00D1FF'];
    if ($score <= 60) return ['text' => 'Medium', 'color' => '#ffa502'];
    if ($score <= 80) return ['text' => 'High', 'color' => '#ff4757'];
    return ['text' => 'Critical', 'color' => '#ff0000'];
}
?>