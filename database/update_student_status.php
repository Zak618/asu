<?php
include_once "db.php";

header('Content-Type: application/json');

$response = ["success" => false, "error" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $status = $_POST['status'];
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
    $direction_code = isset($_POST['direction_code']) ? $_POST['direction_code'] : '';
    $direction_name = isset($_POST['direction_name']) ? $_POST['direction_name'] : '';
    $profile = isset($_POST['profile']) ? $_POST['profile'] : '';

    if ($status == 1) {
        $sql = "UPDATE users SET moderator_status = ?, direction_code = ?, direction_name = ?, profile = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssi", $status, $direction_code, $direction_name, $profile, $student_id);
    } else {
        $sql = "UPDATE users SET moderator_status = ?, moderator_comment = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $status, $comment, $student_id);
    }

    if ($stmt->execute()) {
        $response["success"] = true;
        $response["message"] = "Статус обновлен";
    } else {
        $response["error"] = "Ошибка: " . $conn->error;
    }

    $stmt->close();
} else {
    $response["error"] = "Неверный метод запроса";
}

$conn->close();
echo json_encode($response);
?>
