<?php
include_once "db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
        exit;
    }

    // Проверка наличия certificate_id в POST-запросе
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['certificate_id'])) {
        echo json_encode(['success' => false, 'error' => 'certificate_id не передан']);
        exit;
    }

    $certificate_id = intval($data['certificate_id']);
    $user_id = intval($_SESSION['user_id']);


    // Получение информации о сертификате
    $certificate_sql = "SELECT * FROM certificate WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($certificate_sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Ошибка подготовки запроса: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("ii", $certificate_id, $user_id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Ошибка выполнения запроса: ' . $stmt->error]);
        exit;
    }
    $result = $stmt->get_result();
    $certificate = $result->fetch_assoc();

    if ($certificate) {
        // Удаление файла сертификата
        if (file_exists($certificate['file_path'])) {
            if (!unlink($certificate['file_path'])) {
                echo json_encode(['success' => false, 'error' => 'Ошибка удаления файла']);
                exit;
            }
        }

        // Удаление записи из базы данных
        $delete_sql = "DELETE FROM certificate WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Ошибка подготовки запроса: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("i", $certificate_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка выполнения запроса: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Сертификат не найден']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Некорректный метод запроса']);
}
?>
