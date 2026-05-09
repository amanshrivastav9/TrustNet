<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$website_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Verify ownership
$stmt = $pdo->prepare("SELECT * FROM websites WHERE id = ? AND user_id = ?");
$stmt->execute([$website_id, $user_id]);
$website = $stmt->fetch();

if (!$website) {
    header('Location: websites.php');
    exit();
}

// Tracking code for localhost website
$tracking_code = <<<HTML
<!-- TrustNet Tracking Code -->
<script src="http://localhost/trustnet/assets/js/tracker.js" data-api-key="{$website['api_key']}"></script>
<!-- End TrustNet Tracking Code -->
HTML;

// Alternative tracking code for same-domain
$same_domain_code = <<<HTML
<!-- TrustNet Tracking Code -->
<script>
(function() {{
    const API_URL = '/trustnet/api/track.php';
    const API_KEY = '{$website['api_key']}';
    
    async function track(action, data) {{
        try {{
            await fetch(API_URL, {{
                method: 'POST',
                headers: {{'Content-Type': 'application/json'}},
                body: JSON.stringify({{api_key: API_KEY, action: action, ...data, url: location.href}})
            }});
        }} catch(e) {{ console.log('Track error:', e); }}
    }}
    
    track('pageview', {{}});
    const start = Date.now();
    window.addEventListener('beforeunload', () => track('session', {{duration: Math.floor((Date.now() - start) / 1000)}}));
}})();
</script>
<!-- End TrustNet Tracking Code -->
HTML;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Code - <?php echo htmlspecialchars($website['website_name']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">TrustNet Security</div>
            <div class="nav-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="websites.php">Websites</a>
                <a href="analytics.php">Analytics</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <div class="glass-card">
            <h2>Tracking Code for <?php echo htmlspecialchars($website['website_name']); ?></h2>
            
            <div class="alert alert-info">
                <strong>⚠️ Important:</strong> 
                - If your website is on <strong>localhost</strong>, use the first code<br>
                - If TrustNet is installed in a subfolder, adjust the path accordingly
            </div>
            
            <h3>Option 1: Using tracker.js (Recommended)</h3>
            <p>Copy this code and paste it just before the closing <code>&lt;/body&gt;</code> tag:</p>
            <div class="code-block">
                <pre id="code1"><?php echo htmlspecialchars($tracking_code); ?></pre>
            </div>
            <button onclick="copyCode('code1')" class="btn btn-primary">Copy Code</button>
            
            <h3>Option 2: Manual Implementation</h3>
            <p>Use this if you want more control:</p>
            <div class="code-block">
                <pre id="code2"><?php echo htmlspecialchars($same_domain_code); ?></pre>
            </div>
            <button onclick="copyCode('code2')" class="btn btn-primary">Copy Code</button>
            
            <h3>Your API Information</h3>
            <div class="api-info">
                <p><strong>API Key:</strong> <code><?php echo $website['api_key']; ?></code></p>
                <p><strong>Secret Key:</strong> <code><?php echo $website['secret_key']; ?></code></p>
                <p><strong>API Endpoint:</strong> <code>http://localhost/trustnet/api/track.php</code></p>
            </div>
            
            <h3>Testing Your Integration</h3>
            <p>After adding the code to your website, visit your website and then check:</p>
            <ul>
                <li><a href="website-dashboard.php?id=<?php echo $website['id']; ?>">Website Dashboard</a> - See real-time stats</li>
                <li><a href="analytics.php?website_id=<?php echo $website['id']; ?>">Analytics Page</a> - View detailed analytics</li>
            </ul>
        </div>
    </div>
    
    <script>
        function copyCode(elementId) {
            const code = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(code).then(() => {
                alert('Code copied to clipboard!');
            });
        }
    </script>
    
    <style>
        .api-info {
            background: rgba(0,0,0,0.3);
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .api-info code {
            background: #0A0F1C;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .alert-info {
            background: rgba(0, 209, 255, 0.1);
            border-left: 3px solid #00D1FF;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</body>
</html>