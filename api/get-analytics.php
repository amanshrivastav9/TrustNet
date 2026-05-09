<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/functions.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'recent_activity':
            $stmt = $pdo->prepare("SELECT al.*, w.website_name 
                                   FROM activity_logs al 
                                   JOIN websites w ON al.website_id = w.id 
                                   WHERE w.user_id = ? 
                                   ORDER BY al.timestamp DESC 
                                   LIMIT 20");
            $stmt->execute([$user_id]);
            $logs = $stmt->fetchAll();
            
            $formatted_logs = [];
            foreach ($logs as $log) {
                $formatted_logs[] = [
                    'timestamp' => date('Y-m-d H:i:s', strtotime($log['timestamp'])),
                    'activity_type' => $log['activity_type'],
                    'details' => $log['activity_details'],
                    'website' => $log['website_name']
                ];
            }
            
            echo json_encode(['status' => 'success', 'logs' => $formatted_logs]);
            break;
            
        case 'website_stats':
            $website_id = $_GET['website_id'] ?? 0;
            
            $stmt = $pdo->prepare("SELECT 
                                   COUNT(DISTINCT ll.user_identifier) as unique_visitors,
                                   COUNT(ll.id) as total_visits,
                                   COUNT(DISTINCT ll.ip_address) as unique_ips
                                   FROM login_logs ll
                                   WHERE ll.website_id = ?");
            $stmt->execute([$website_id]);
            $stats = $stmt->fetch();
            
            echo json_encode(['status' => 'success', 'stats' => $stats]);
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>