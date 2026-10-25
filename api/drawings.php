<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'save':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        $cell_data = $_POST['cell_data'] ?? '';
        if (!$cell_data) { echo json_encode(['error' => 'No data']); exit; }
       
        $decoded = json_decode($cell_data, true);
        if (!is_array($decoded)) { echo json_encode(['error' => 'Invalid data']); exit; }
        $uid  = (int)$_SESSION['user_id'];
        $stmt = $conn->prepare('INSERT INTO drawings (user_id, cell_data) VALUES (?, ?)');
        $stmt->bind_param('is', $uid, $cell_data);
        $stmt->execute();
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
        break;

    case 'list':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        $uid  = (int)$_SESSION['user_id'];
        $stmt = $conn->prepare('SELECT id, cell_data, created_at FROM drawings WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $result = $stmt->get_result();
        $drawings = [];
        while ($row = $result->fetch_assoc()) {
            $row['cell_data'] = json_decode($row['cell_data'], true);
            $drawings[] = $row;
        }
        echo json_encode($drawings);
        break;

    case 'delete':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        $id  = (int)($_GET['id'] ?? 0);
        $uid = (int)$_SESSION['user_id'];
        $stmt = $conn->prepare('DELETE FROM drawings WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $id, $uid);
        $stmt->execute();
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}
