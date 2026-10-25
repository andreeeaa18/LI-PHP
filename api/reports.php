<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'report':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        $post_id = (int)($_POST['post_id'] ?? 0);
        $uid     = (int)$_SESSION['user_id'];
        $reason  = sanitize($_POST['reason'] ?? 'Inappropriate content');
        $stmt = $conn->prepare('INSERT IGNORE INTO reports (post_id, user_id, reason) VALUES (?, ?, ?)');
        $stmt->bind_param('iis', $post_id, $uid, $reason);
        $stmt->execute();
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}
