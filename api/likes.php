<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'toggle':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        $post_id = (int)($_POST['post_id'] ?? 0);
        $uid     = (int)$_SESSION['user_id'];

        $check = $conn->prepare('SELECT id FROM likes WHERE post_id = ? AND user_id = ?');
        $check->bind_param('ii', $post_id, $uid);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $del = $conn->prepare('DELETE FROM likes WHERE post_id = ? AND user_id = ?');
            $del->bind_param('ii', $post_id, $uid);
            $del->execute();
            $liked = false;
        } else {
            $ins = $conn->prepare('INSERT INTO likes (post_id, user_id) VALUES (?, ?)');
            $ins->bind_param('ii', $post_id, $uid);
            $ins->execute();
            $liked = true;
        }
        $cnt = $conn->prepare('SELECT COUNT(*) AS c FROM likes WHERE post_id = ?');
        $cnt->bind_param('i', $post_id);
        $cnt->execute();
        $count = $cnt->get_result()->fetch_assoc()['c'];
        echo json_encode(['liked' => $liked, 'count' => $count]);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}
