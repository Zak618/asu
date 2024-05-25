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


    $sql = "INSERT INTO students (first_name, last_name, middle_name, group_name, email, phone_number, password)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $firstName, $lastName, $middleName, $groupName, $email, $phoneNumber, $password);

    if ($stmt->execute()) {
        echo "Регистрация успешна!";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>