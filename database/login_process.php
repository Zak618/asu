<?php
include_once "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['avatar_url'] = $user['avatar_url'];
            $_SESSION['balance'] = $user['balance'];
            $_SESSION['group_name'] = $user['group_name'];
            $_SESSION['direction_code'] = $user['direction_code'];
            $_SESSION['direction_name'] = $user['direction_name'];
            $_SESSION['profile'] = $user['profile'];
            $_SESSION['moderator_status'] = $user['moderator_status'];
            $_SESSION['moderator_comment'] = $user['moderator_comment'];
            $_SESSION['phone_number'] = $user['phone_number'];
            header("Location: ../profile.php");
            exit();
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
