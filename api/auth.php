<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'signup':
        if ($method !== 'POST') { echo json_encode(['error' => 'POST required']); exit; }
        $name     = sanitize($_POST['name'] ?? '');
        $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$name || !$email || strlen($password) < 6) {
            echo json_encode(['error' => 'Invalid input. Password must be at least 6 characters.']);
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $name, $email, $hash);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Email already exists.']);
        }
        break;

    case 'login':
        if ($method !== 'POST') { echo json_encode(['error' => 'POST required']); exit; }
        $name     = sanitize($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$name || !$password) {
            echo json_encode(['error' => 'Name and password required.']);
            exit;
        }

        $stmt = $conn->prepare('SELECT id, name, password, role FROM users WHERE name = ?');
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            echo json_encode(['success' => true, 'name' => $user['name'], 'role' => $user['role']]);
        } else {
            echo json_encode(['error' => 'Invalid credentials.']);
        }
        break;

    case 'logout':
        session_destroy();
        echo json_encode(['success' => true]);
        break;

    case 'me':
        if (!isLoggedIn()) { echo json_encode(['error' => 'Not logged in']); exit; }
        echo json_encode(currentUser());
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}
