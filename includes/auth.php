<?php
/**
 * Authentication Helper
 * Handles user authentication and authorization
 */

require_once 'config.php';
require_once 'functions.php';
require_once 'session.php';

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isSessionValid();
}

/**
 * Require login (redirect if not logged in)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . 'login.php');
        exit();
    }
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireLogin();
    if ($_SESSION['user_role'] !== 'admin') {
        header('Location: ' . SITE_URL . 'dashboard.php');
        exit();
    }
}

/**
 * Get current user data
 */
function getCurrentUser($pdo) {
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Login user
 */
function loginUser($pdo, $email, $password, $remember = false) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        if (!$user['email_verified']) {
            return ['status' => false, 'message' => 'Please verify your email first'];
        }
        
        setUserSession($user);
        
        // Remember me (7 days)
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+7 days'));
            
            $stmt = $pdo->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expires]);
            
            setcookie('remember_token', $token, strtotime('+7 days'), '/', '', false, true);
        }
        
        return ['status' => true, 'user' => $user];
    }
    
    return ['status' => false, 'message' => 'Invalid email or password'];
}

/**
 * Check remember me token
 */
function checkRememberToken($pdo) {
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        $stmt = $pdo->prepare("SELECT u.* FROM users u 
                               JOIN user_tokens t ON u.id = t.user_id 
                               WHERE t.token = ? AND t.expires_at > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            setUserSession($user);
            return true;
        }
    }
    return false;
}

/**
 * Logout user
 */
function logoutUser($pdo) {
    // Delete remember token if exists
    if (isset($_COOKIE['remember_token']) && isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("DELETE FROM user_tokens WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    destroySession();
}

/**
 * Check user permission
 */
function hasPermission($pdo, $userId, $permission) {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    $permissions = [
        'admin' => ['all'],
        'user' => ['view_dashboard', 'manage_websites', 'view_analytics']
    ];
    
    return in_array($permission, $permissions[$user['role']] ?? []) || in_array('all', $permissions[$user['role']] ?? []);
}

/**
 * Rate limit login attempts
 */
function checkLoginRateLimit($pdo, $email, $ip) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as attempts, MAX(created_at) as last_attempt 
                           FROM failed_attempts 
                           WHERE (email = ? OR ip_address = ?) 
                           AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $stmt->execute([$email, $ip]);
    $result = $stmt->fetch();
    
    if ($result['attempts'] >= 5) {
        return ['allowed' => false, 'wait_minutes' => 15];
    }
    
    return ['allowed' => true, 'attempts_remaining' => 5 - $result['attempts']];
}

/**
 * Log failed login attempt
 */
function logFailedLoginAttempt($pdo, $email, $ip) {
    $stmt = $pdo->prepare("INSERT INTO failed_attempts (ip_address, email, attempt_count, created_at) 
                           VALUES (?, ?, 1, NOW())");
    return $stmt->execute([$ip, $email]);
}

/**
 * Clear failed login attempts
 */
function clearFailedLoginAttempts($pdo, $email, $ip) {
    $stmt = $pdo->prepare("DELETE FROM failed_attempts WHERE email = ? OR ip_address = ?");
    return $stmt->execute([$email, $ip]);
}
?>