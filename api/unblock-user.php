<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
require_once '../includes/functions.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$block_id = $input['block_id'] ?? 0;

if (!$block_id) {
    echo json_encode(['status' => 'error', 'message' => 'Block ID required']);
    exit();
}

// Verify permission
$stmt = $pdo->prepare("SELECT b.*, w.user_id as owner_id 
                       FROM api_user_blocks b 
                       JOIN websites w ON b.website_id = w.id 
                       WHERE b.id = ?");
$stmt->execute([$block_id]);
$block = $stmt->fetch();

if (!$block) {
    echo json_encode(['status' => 'error', 'message' => 'Block record not found']);
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

if ($user_role != 'admin' && $block['owner_id'] != $user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Permission denied']);
    exit();
}

// Unblock user
$stmt = $pdo->prepare("UPDATE api_user_blocks SET is_blocked = 0, unblocked_at = NOW() WHERE id = ?");
$stmt->execute([$block_id]);

logSecurityEvent($pdo, $block['website_id'], 'user_unblocked', 'medium', $block['user_email'], getUserIP(),
                "User manually unblocked by " . ($user_role == 'admin' ? 'admin' : 'website owner'));

echo json_encode(['status' => 'success', 'message' => 'User unblocked successfully']);
?>