<?php
include_once "db.php";
require '../vendor/autoload.php'; // Путь к автозагрузчику Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Проверка, существует ли пользователь с таким email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Генерация уникального токена для сброса пароля
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Сохранение токена в базе данных
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expiry);
        $stmt->execute();

        // Настройка PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Настройки сервера
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = ''; // email
            $mail->Password = ''; // пароль
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Получатели
            $mail->setFrom('rolanzakirov@mail.ru', 'No Reply');
            $mail->addAddress($email);

            // Контент письма
            $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token;
            $mail->isHTML(true);
            $mail->Subject = 'Сброс пароля';
            $mail->Body    = "Для сброса пароля перейдите по следующей ссылке: <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['status' => 'error']);
    }

    $stmt->close();
    $conn->close();
}
?>
