<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'trustnet_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('SITE_URL', 'http://localhost/trustnet/');
define('SITE_NAME', 'TrustNet Security Platform');

// Email configuration (PHPMailer)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'amanshrivastav2010@gmail.com'); // Replace with your email
define('SMTP_PASS', 'duki mluz wtpw uxzp'); // Replace with Gmail app password
define('SMTP_FROM', 'amanshrivastav2010@gmail.com');
define('SMTP_FROM_NAME', 'TrustNet Security');

// Security configuration
define('BCRYPT_COST', 12);
define('SESSION_TIMEOUT', 3600); // 1 hour
define('RATE_LIMIT_REQUESTS', 100);
define('RATE_LIMIT_MINUTES', 1);

// API configuration
define('JWT_SECRET', 'your-secret-key-change-this-to-random-string');
define('API_RATE_LIMIT', 100); // requests per minute

// Database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Timezone
date_default_timezone_set('Asia/Kolkata');
?>