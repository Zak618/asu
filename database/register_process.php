<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $middleName = $_POST['middleName'] ?? '';
    $groupName = $_POST['groupName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Хеширование пароля

    // Генерация URL для аватара
    $seed = urlencode($firstName . ' ' . $lastName);
    $avatarUrl = "https://api.dicebear.com/8.x/initials/svg?seed=$seed&radius=50";

    $sql = "INSERT INTO students (first_name, last_name, middle_name, group_name, email, phone_number, password, avatar_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $firstName, $lastName, $middleName, $groupName, $email, $phoneNumber, $password, $avatarUrl);

    if ($stmt->execute()) {
        echo "Регистрация успешна!";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
