<?php
include_once "db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['moderator_id'])) {
        echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
        exit;
    }

    $id = intval($_POST['id']);
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_name = $_POST['middle_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($password) {
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET first_name = ?, last_name = ?, middle_name = ?, email = ?, password = ? WHERE id = ? AND role = 2";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $first_name, $last_name, $middle_name, $email, $password_hashed, $id);
    } else {
        $sql = "UPDATE users SET first_name = ?, last_name = ?, middle_name = ?, email = ? WHERE id = ? AND role = 2";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $first_name, $last_name, $middle_name, $email, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении данных преподавателя: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Некорректный метод запроса']);
}
?>
