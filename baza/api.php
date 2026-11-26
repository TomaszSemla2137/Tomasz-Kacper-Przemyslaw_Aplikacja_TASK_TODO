<?php
header('Content-Type: application/json; charset=utf-8');

// Konfiguracja połączenia z lokalnym MariaDB w XAMPP
$host = '127.0.0.1';
$db   = 'todo_app';
$user = 'root';
$pass = ''; // Domyślne puste hasło w XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Błąd połączenia z bazą: ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Pobieranie listy zadań (READ)
if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll());
}

// Dodawanie nowego zadania (CREATE)
elseif ($method === 'POST' && !empty($input['title'])) {
    $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (:title)");
    $stmt->execute(['title' => htmlspecialchars(trim($input['title']))]);
    echo json_encode(['id' => $pdo->lastInsertId(), 'status' => 'created']);
}

// Zmiana statusu wykonania (UPDATE)
elseif ($method === 'PATCH' && isset($input['id'], $input['is_completed'])) {
    $stmt = $pdo->prepare("UPDATE tasks SET is_completed = :status WHERE id = :id");
    $stmt->execute([
        'status' => (int)$input['is_completed'],
        'id'     => (int)$input['id']
    ]);
    echo json_encode(['status' => 'updated']);
}

// Usuwanie zadania (DELETE)
elseif ($method === 'DELETE' && isset($input['id'])) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => (int)$input['id']]);
    echo json_encode(['status' => 'deleted']);
}
?>