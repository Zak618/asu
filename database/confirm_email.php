<?php
include_once "db.php";
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Поиск пользователя с таким токеном
    $stmt = $conn->prepare("SELECT id FROM users WHERE email_confirm_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userId = $user['id'];

        // Обновление статуса email подтверждения
        $stmt = $conn->prepare("UPDATE users SET email_confirm_token = NULL WHERE id = ?");
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {
            $_SESSION['confirmation_message'] = "Ваш email успешно подтвержден!";
            header("Location: http://localhost:8000/login.php?confirmed=1");
            exit();
        } else {
            echo "Ошибка при подтверждении email. Попробуйте еще раз.";
        }
    } else {
        echo "Неверный токен подтверждения.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Отсутствует токен подтверждения.";
}
?>

