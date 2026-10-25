<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $user_id = $_SESSION['user_id'] ?? 0;
        $stmt = $conn->prepare('
            SELECT p.id, p.title, p.content, p.image_url, p.created_at,
                   u.name AS author, u.id AS author_id,
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) AS user_liked
            FROM posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
        ');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $row['user_liked'] = (bool)$row['user_liked'];
            $posts[] = $row;
        }
        echo json_encode($posts);
        break;

    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        $user_id = $_SESSION['user_id'] ?? 0;
        $stmt = $conn->prepare('
            SELECT p.id, p.title, p.content, p.image_url, p.created_at,
                   u.name AS author, u.id AS author_id,
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count,
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) AS user_liked
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ');
        $stmt->bind_param('ii', $user_id, $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) {
            $row['user_liked'] = (bool)$row['user_liked'];
            echo json_encode($row);
        } else {
            echo json_encode(['error' => 'Not found']);
        }
        break;

    case 'create':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        if ($method !== 'POST') { echo json_encode(['error' => 'POST required']); exit; }
        $title   = sanitize($_POST['title'] ?? '');
        $content = sanitize($_POST['content'] ?? '');
        $image   = filter_var(trim($_POST['image_url'] ?? ''), FILTER_SANITIZE_URL);
        $uid     = (int)$_SESSION['user_id'];

        if (!$title || !$content) { echo json_encode(['error' => 'Title and content required']); exit; }

        $stmt = $conn->prepare('INSERT INTO posts (user_id, title, content, image_url) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('isss', $uid, $title, $content, $image);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['error' => 'Could not create post']);
        }
        break;

    case 'update':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        if ($method !== 'POST') { echo json_encode(['error' => 'POST required']); exit; }
        $id      = (int)($_POST['id'] ?? 0);
        $title   = sanitize($_POST['title'] ?? '');
        $content = sanitize($_POST['content'] ?? '');
        $image   = filter_var(trim($_POST['image_url'] ?? ''), FILTER_SANITIZE_URL);
        $uid     = (int)$_SESSION['user_id'];
        $role    = $_SESSION['user_role'] ?? 'user';

        if (!$title || !$content) { echo json_encode(['error' => 'Title and content required']); exit; }

        if ($role === 'admin') {
            $stmt = $conn->prepare('UPDATE posts SET title = ?, content = ?, image_url = ? WHERE id = ?');
            $stmt->bind_param('sssi', $title, $content, $image, $id);
        } else {
            $stmt = $conn->prepare('UPDATE posts SET title = ?, content = ?, image_url = ? WHERE id = ? AND user_id = ?');
            $stmt->bind_param('sssii', $title, $content, $image, $id, $uid);
        }
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Not found or not your post']);
        }
        break;

    case 'delete':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        $id  = (int)($_GET['id'] ?? 0);
        $uid = (int)$_SESSION['user_id'];
        $role = $_SESSION['user_role'] ?? 'user';

        if ($role === 'admin') {
            $stmt = $conn->prepare('DELETE FROM posts WHERE id = ?');
            $stmt->bind_param('i', $id);
        } else {
            $stmt = $conn->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
            $stmt->bind_param('ii', $id, $uid);
        }
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Not found or not your post']);
        }
        break;

    case 'user_posts':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        $uid = (int)$_SESSION['user_id'];
        $stmt = $conn->prepare('
            SELECT p.id, p.title, p.content, p.image_url, p.created_at,
                   (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count,
                   (SELECT COUNT(*) FROM comments WHERE post_id = p.id) AS comment_count
            FROM posts p WHERE p.user_id = ? ORDER BY p.created_at DESC
        ');
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = [];
        while ($row = $result->fetch_assoc()) $posts[] = $row;
        echo json_encode($posts);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}
