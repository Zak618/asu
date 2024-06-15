<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Валидация паролей
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Пароли не совпадают.']);
        exit;
    }

    // Поиск токена в базе данных
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Ошибка подготовки запроса.']);
        exit;
    }

    $stmt->bind_param("s", $token);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Ошибка выполнения запроса.']);
        exit;
    }

    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();

        // Обновление пароля пользователя
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt->close();

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка подготовки запроса для обновления пароля.']);
            exit;
        }

        $stmt->bind_param("ss", $hashedPassword, $email);
        if ($stmt->execute()) {
            // Удаление использованного токена
            $stmt->close();
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            if (!$stmt) {
                echo json_encode(['status' => 'error', 'message' => 'Ошибка подготовки запроса для удаления токена.']);
                exit;
            }

            $stmt->bind_param("s", $email);
            $stmt->execute();

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка при обновлении пароля.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Неверный или просроченный токен.']);
    }

    $stmt->close();
    $conn->close();
}
?>
