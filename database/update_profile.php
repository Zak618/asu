<?php
include_once "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $groupName = $_POST['groupName'];

    // Валидация данных
    $errors = [];

    // Проверка имени и фамилии
    if (empty($firstName)) {
        $errors[] = "Поле имя не должно быть пустым.";
    } elseif (preg_match('/\d/', $firstName)) {
        $errors[] = "Поле имя не должно содержать цифры.";
    } elseif (strlen($firstName) > 25) {
        $errors[] = "Поле имя не должно превышать 25 символов.";
    }

    if (empty($lastName)) {
        $errors[] = "Поле фамилия не должно быть пустым.";
    } elseif (preg_match('/\d/', $lastName)) {
        $errors[] = "Поле фамилия не должно содержать цифры.";
    } elseif (strlen($lastName) > 25) {
        $errors[] = "Поле фамилия не должно превышать 25 символов.";
    }

    if (empty($email)) {
        $errors[] = "Поле email не должно быть пустым.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введите действительный email адрес.";
    } elseif (strlen($email) > 255) {
        $errors[] = "Поле email не должно превышать 255 символов.";
    }

    if (empty($groupName)) {
        $errors[] = "Поле группа не должно быть пустым.";
    } elseif (preg_match('/\d/', $groupName)) {
        $errors[] = "Поле группа не должно содержать цифры.";
    } elseif (strlen($groupName) > 25) {
        $errors[] = "Поле группа не должно превышать 25 символов.";
    }

    // Если есть ошибки, вернуть их пользователю
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'messages' => $errors]);
        exit;
    }

    $avatarUrl = $_SESSION['avatar_url'];
    if (!empty($_FILES['avatar']['name'])) {
        $targetDir = "../images/avatars/";
        $targetFile = $targetDir . basename($_FILES["avatar"]["name"]);
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)) {
            $avatarUrl = $targetFile;
        }
    }

    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, group_name = ?, avatar_url = ?, moderator_status = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $firstName, $lastName, $email, $groupName, $avatarUrl, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;
        $_SESSION['user_email'] = $email;
        $_SESSION['group_name'] = $groupName;
        $_SESSION['avatar_url'] = $avatarUrl;
        $_SESSION['moderator_status'] = 0;
        echo json_encode(['status' => 'success', 'redirect' => 'profile']);
    } else {
        echo json_encode(['status' => 'error', 'messages' => ["Ошибка: " . $sql . "<br>" . $conn->error]]);
    }

    $stmt->close();
    $conn->close();
}
?>
