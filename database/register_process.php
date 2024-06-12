<?php
include_once "db.php";

// Функция для проверки временного email-домена
function isDisposableEmail($email) {
    $disposableDomains = file('https://raw.githubusercontent.com/andreis/disposable-email-domains/master/domains.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emailDomain = substr(strrchr($email, "@"), 1);
    return in_array($emailDomain, $disposableDomains);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $middleName = $_POST['middleName'] ?? '';
    $groupName = $_POST['groupName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Валидация данных
    $errors = [];

    // Проверка имени и фамилии
    if (preg_match('/\d/', $firstName) || strlen($firstName) > 25) {
        $errors[] = "Имя не должно содержать цифры и превышать 25 символов.";
    }
    if (preg_match('/\d/', $lastName) || strlen($lastName) > 25) {
        $errors[] = "Фамилия не должна содержать цифры и превышать 25 символов.";
    }
    if (!empty($middleName) && (preg_match('/\d/', $middleName) || strlen($middleName) > 25)) {
        $errors[] = "Отчество не должно содержать цифры и превышать 25 символов.";
    }

    // Проверка email
    if (isDisposableEmail($email)) {
        $errors[] = "Временные email-адреса не допускаются.";
    } else {
        // Проверка существующего email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Этот email уже используется.";
        }
        $stmt->close();
    }

    // Проверка совпадения паролей
    if ($password !== $confirmPassword) {
        $errors[] = "Пароли не совпадают.";
    }

    // Если есть ошибки, вернуть их пользователю
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'messages' => $errors]);
    } else {
        // Если ошибок нет, продолжаем регистрацию

        // Хеширование пароля
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Генерация URL для аватара
        $seed = urlencode($firstName . ' ' . $lastName);
        $avatarUrl = "https://api.dicebear.com/8.x/initials/svg?seed=$seed&radius=50";

        // Установка роли по умолчанию
        $role = 1;

        $sql = "INSERT INTO users (first_name, last_name, middle_name, group_name, email, phone_number, password, avatar_url, role)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $firstName, $lastName, $middleName, $groupName, $email, $phoneNumber, $hashedPassword, $avatarUrl, $role);

        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'messages' => ["Ошибка: " . $sql . "<br>" . $conn->error]]);
        }

        $stmt->close();
        $conn->close();
    }
}
?>
