<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $post_id = (int)($_GET['post_id'] ?? 0);
        $stmt = $conn->prepare('
            SELECT c.id, c.content, c.parent_id, c.created_at, u.name AS author, u.id AS author_id
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at ASC
        ');
        $stmt->bind_param('i', $post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = [];
        while ($row = $result->fetch_assoc()) $comments[] = $row;
        echo json_encode($comments);
        break;

    case 'add':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        if ($method !== 'POST') { echo json_encode(['error' => 'POST required']); exit; }
        $post_id   = (int)($_POST['post_id'] ?? 0);
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $content   = sanitize($_POST['content'] ?? '');
        $uid       = (int)$_SESSION['user_id'];

        if (!$content) { echo json_encode(['error' => 'Comment is empty']); exit; }

        $stmt = $conn->prepare('INSERT INTO comments (post_id, user_id, parent_id, content) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('iiis', $post_id, $uid, $parent_id, $content);
        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $row = $conn->query("SELECT c.id, c.content, c.parent_id, c.created_at, u.name AS author, u.id AS author_id FROM comments c JOIN users u ON c.user_id = u.id WHERE c.id = $new_id")->fetch_assoc();
            echo json_encode(['success' => true, 'comment' => $row]);
        } else {
            echo json_encode(['error' => 'Could not add comment']);
        }
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}
