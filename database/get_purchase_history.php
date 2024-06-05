<?php
include_once "db.php";

$user_id = $_GET['user_id'];

$sql = "SELECT hm.*, m.item_name, m.price, u.first_name AS teacher_first_name, u.last_name AS teacher_last_name
        FROM history_market hm
        JOIN market m ON hm.item_id = m.id
        LEFT JOIN users u ON hm.teacher_id = u.id
        WHERE hm.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

echo json_encode($history);
?>
