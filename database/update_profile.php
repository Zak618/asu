<?php
include_once "db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $groupName = $_POST['groupName'];

    $avatarUrl = $_SESSION['avatar_url'];
    if (!empty($_FILES['avatar']['name'])) {
        $targetDir = "../images/avatars/";
        $targetFile = $targetDir . basename($_FILES["avatar"]["name"]);
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)) {
            $avatarUrl = $targetFile;
        }
    }

    $sql = "UPDATE students SET first_name = ?, last_name = ?, email = ?, phone_number = ?, group_name = ?, avatar_url = ?, moderator_status = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $firstName, $lastName, $email, $phoneNumber, $groupName, $avatarUrl, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $_SESSION['first_name'] = $firstName;
        $_SESSION['last_name'] = $lastName;
        $_SESSION['user_email'] = $email;
        $_SESSION['phone_number'] = $phoneNumber;
        $_SESSION['group_name'] = $groupName;
        $_SESSION['avatar_url'] = $avatarUrl;
        $_SESSION['moderator_status'] = 0;
        header("Location: ../profile.php");
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
