<?php
/**
 * Session Management System
 * Handles secure session initialization and management
 */

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Regenerate session ID to prevent fixation
 */
function regenerateSession() {
    session_regenerate_id(true);
    
    // Update session cookie
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        session_id(),
        time() + SESSION_TIMEOUT,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

/**
 * Check if session is valid
 */
function isSessionValid() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        destroySession();
        return false;
    }
    
    // Update last activity
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Set session data for logged in user
 */
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['ip_address'] = getUserIP();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    
    regenerateSession();
}

/**
 * Destroy session completely
 */
function destroySession() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    
    session_destroy();
}

/**
 * Check if session IP matches (prevent session hijacking)
 */
function validateSessionIP() {
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== getUserIP()) {
        destroySession();
        return false;
    }
    return true;
}

/**
 * Check if session user agent matches
 */
function validateSessionUserAgent() {
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        destroySession();
        return false;
    }
    return true;
}

/**
 * Get session timeout in seconds
 */
function getSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        return SESSION_TIMEOUT - (time() - $_SESSION['last_activity']);
    }
    return SESSION_TIMEOUT;
}

/**
 * Extend session timeout
 */
function extendSession() {
    $_SESSION['last_activity'] = time();
}
?>