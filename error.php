<?php
$code = $_GET['code'] ?? 404;
$messages = [
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Page Not Found',
    500 => 'Internal Server Error'
];
$message = $messages[$code] ?? 'An error occurred';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?php echo $code; ?> - TrustNet Security</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="glass-card" style="text-align: center;">
            <h1 style="font-size: 72px; color: #00D1FF;"><?php echo $code; ?></h1>
            <h2><?php echo $message; ?></h2>
            <p style="margin: 20px 0;">The page you're looking for doesn't exist or you don't have permission to access it.</p>
            <a href="/trustnet/" class="btn btn-primary">Go to Homepage</a>
        </div>
    </div>
</body>
</html>