<?php
include_once "db.php";

if (!defined('ACCESS_ALLOWED')) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Access denied';
    exit;
}

$coupon_id = $_POST['coupon_id'];
$teacher_id = $_POST['teacher_id'];

$sql = "UPDATE history_market SET status = 'Неактивно', teacher_id = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $teacher_id, $coupon_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка активации купона.']);
}
?>
