<?php
include_once "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
        exit;
    }
    
    if (!isset($input['event_id']) || !isset($input['action'])) {
        echo json_encode(['success' => false, 'error' => 'Недостаточно данных']);
        exit;
    }

    $student_id = intval($_SESSION['user_id']);
    $event_id = intval($input['event_id']);
    $action = $input['action'];

    if ($action === 'participate') {
        $stmt = $conn->prepare("INSERT INTO event_participation (student_id, event_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $event_id);
    } elseif ($action === 'cancel') {
        $stmt = $conn->prepare("DELETE FROM event_participation WHERE student_id = ? AND event_id = ?");
        $stmt->bind_param("ii", $student_id, $event_id);
    } else {
        echo json_encode(['success' => false, 'error' => 'Некорректное действие']);
        exit;
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Некорректный метод запроса']);
}
?>
