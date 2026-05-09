<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$format = $_GET['format'] ?? 'csv';
$website_id = $_GET['website_id'] ?? 0;
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get website data
if ($website_id) {
    $stmt = $pdo->prepare("SELECT * FROM websites WHERE id = ? AND user_id = ?");
    $stmt->execute([$website_id, $user_id]);
    $website = $stmt->fetch();
    
    if (!$website) {
        die('Invalid website');
    }
    
    // Get analytics data
    $stmt = $pdo->prepare("SELECT 
                           DATE(login_time) as date,
                           COUNT(*) as total_visits,
                           COUNT(DISTINCT user_identifier) as unique_visitors,
                           COUNT(DISTINCT ip_address) as unique_ips
                           FROM login_logs 
                           WHERE website_id = ? AND DATE(login_time) BETWEEN ? AND ?
                           GROUP BY DATE(login_time)
                           ORDER BY date DESC");
    $stmt->execute([$website_id, $start_date, $end_date]);
    $data = $stmt->fetchAll();
    
    if ($format == 'csv') {
        // Export as CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="analytics_' . $website['website_name'] . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Total Visits', 'Unique Visitors', 'Unique IPs']);
        
        foreach ($data as $row) {
            fputcsv($output, [$row['date'], $row['total_visits'], $row['unique_visitors'], $row['unique_ips']]);
        }
        
        fclose($output);
    } elseif ($format == 'json') {
        // Export as JSON
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="analytics_' . $website['website_name'] . '_' . date('Y-m-d') . '.json"');
        
        echo json_encode([
            'website' => $website['website_name'],
            'url' => $website['website_url'],
            'period' => ['start' => $start_date, 'end' => $end_date],
            'data' => $data,
            'exported_at' => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT);
    }
} else {
    // Export all websites data
    $stmt = $pdo->prepare("SELECT w.website_name, COUNT(ll.id) as total_visits, COUNT(DISTINCT ll.user_identifier) as unique_visitors
                           FROM websites w
                           LEFT JOIN login_logs ll ON w.id = ll.website_id
                           WHERE w.user_id = ?
                           GROUP BY w.id");
    $stmt->execute([$user_id]);
    $data = $stmt->fetchAll();
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="all_websites_analytics_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Website Name', 'Total Visits', 'Unique Visitors']);
    
    foreach ($data as $row) {
        fputcsv($output, [$row['website_name'], $row['total_visits'], $row['unique_visitors']]);
    }
    
    fclose($output);
}
?>