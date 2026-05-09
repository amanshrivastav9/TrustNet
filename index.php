<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header('Location: admin-panel.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrustNet - Advanced Security Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="hero-section">
        <div class="hero-content">
            <h1 class="glitch-text">TrustNet Security Platform</h1>
            <p class="hero-subtitle">Advanced protection for your digital assets with AI-powered threat detection</p>
            <div class="hero-buttons">
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-secondary">Get Started</a>
            </div>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>API Security</h3>
                <p>4 failed attempts = 5 hours block with specific user ID tracking</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🤖</div>
                <h3>AI Trust Engine</h3>
                <p>Real-time risk scoring based on user behavior patterns</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Real-time Analytics</h3>
                <p>Live dashboard with Chart.js visualizations</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🌍</div>
                <h3>Geo Tracking</h3>
                <p>IP-based location tracking and threat detection</p>
            </div>
        </div>
    </div>
</body>
</html>