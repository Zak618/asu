<?php
include_once "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            if ($user['role'] == 3) { // Проверка роли
                $_SESSION['moderator_id'] = $user['id'];
                $_SESSION['moderator_email'] = $user['email'];
                header("Location: ../admin_dashboard.php");
                exit();
            } else {
                echo "У вас нет прав доступа.";
            }
        } else {
            echo "Неверный пароль.";
        }
    } else {
        echo "Пользователь с таким email не найден.";
    }

    $stmt->close();
    $conn->close();
}
?>
