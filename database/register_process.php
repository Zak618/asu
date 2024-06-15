<?php
include_once "db.php";
require '../vendor/autoload.php'; // Если используете Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Функция для проверки временного email-домена
function isDisposableEmail($email) {
    $disposableDomains = file('https://raw.githubusercontent.com/andreis/disposable-email-domains/master/domains.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emailDomain = substr(strrchr($email, "@"), 1);
    return in_array($emailDomain, $disposableDomains);
}

// Функция для отправки письма
function sendConfirmationEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // Настройки сервера
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'webhacker.sup@gmail.com'; // Ваш Gmail логин
        $mail->Password = 'ifwv lcuy lbly uwtv'; // Ваш Gmail пароль
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587; // Используйте порт 587 для TLS

        // Установить кодировку
        $mail->CharSet = 'UTF-8';
        $mail->setLanguage('ru', '../vendor/phpmailer/phpmailer/language/');

        // Получатель
        $mail->setFrom('webhacker.sup@gmail.com', 'ASU');
        $mail->addAddress($email);

        // Содержимое
        $mail->isHTML(true);
        $mail->Subject = 'Подтверждение email';
        $mail->Body = "Пожалуйста, подтвердите ваш email, перейдя по следующей ссылке: ";
        $mail->Body .= "<a href='http://localhost:8000/database/confirm_email.php?token=$token'>Подтвердить email</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Ошибка при отправке письма: {$mail->ErrorInfo}");
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $middleName = $_POST['middleName'] ?? '';
    $groupName = $_POST['groupName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Валидация данных
    $errors = [];

    // Проверка имени и фамилии
    if (empty($firstName)) {
        $errors[] = "Поле Имя не должно быть пустым.";
    } elseif (preg_match('/\d/', $firstName) || strlen($firstName) > 25) {
        $errors[] = "Имя не должно содержать цифры и превышать 25 символов.";
    }
    
    if (empty($lastName)) {
        $errors[] = "Поле Фамилия не должно быть пустым.";
    } elseif (preg_match('/\d/', $lastName) || strlen($lastName) > 25) {
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

    // Проверка пароля
    if (empty($password)) {
        $errors[] = "Поле Пароль не должно быть пустым.";
    } elseif ($password !== $confirmPassword) {
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

        // Генерация токена подтверждения email
        $token = bin2hex(random_bytes(16));

        // Вставка данных пользователя в базу данных
        $sql = "INSERT INTO users (first_name, last_name, middle_name, group_name, email, password, avatar_url, role, email_confirm_token)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $firstName, $lastName, $middleName, $groupName, $email, $hashedPassword, $avatarUrl, $role, $token);

        if ($stmt->execute()) {
            // Отправка письма с подтверждением
            if (sendConfirmationEmail($email, $token)) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'messages' => ["Ошибка при отправке письма подтверждения."]]);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'messages' => ["Ошибка: " . $sql . "<br>" . $conn->error]]);
        }

        $stmt->close();
        $conn->close();
    }
}
?>
