<?php
include_once "db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
        exit;
    }

    $student_id = intval($_SESSION['user_id']);
    $event_id = intval($_POST['event_id']);
    $place = $_POST['place'];

    $target_dir = "../images/certificates/";
    $file_name = basename($_FILES["certificateImage"]["name"]);
    $target_file = $target_dir . uniqid() . "_" . $file_name;
    $upload_ok = 1;
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["certificateImage"]["tmp_name"]);
    if ($check === false) {
        echo json_encode(['success' => false, 'error' => 'Файл не является изображением']);
        $upload_ok = 0;
    }

    if ($_FILES["certificateImage"]["size"] > 5000000) {
        echo json_encode(['success' => false, 'error' => 'Файл слишком большой']);
        $upload_ok = 0;
    }

    if ($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg" && $image_file_type != "gif") {
        echo json_encode(['success' => false, 'error' => 'Только JPG, JPEG, PNG и GIF файлы разрешены']);
        $upload_ok = 0;
    }

    if ($upload_ok == 0) {
        echo json_encode(['success' => false, 'error' => 'Файл не был загружен']);
    } else {
        if (move_uploaded_file($_FILES["certificateImage"]["tmp_name"], $target_file)) {
            $guid = uniqid();
            $stmt = $conn->prepare("INSERT INTO certificate (event_id, user_id, file_path, guid, place) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $event_id, $student_id, $target_file, $guid, $place);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Произошла ошибка при загрузке файла']);
        }
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Некорректный метод запроса']);
}
?>
