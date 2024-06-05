<?php
include_once "db.php";

$user_id = $_GET['user_id'];

$sql = "SELECT h.id as coupon_id, m.id as item_id, m.item_name, m.price, m.color_class, h.status 
        FROM market m
        JOIN history_market h ON m.id = h.item_id
        WHERE h.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$certificates = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $certificates[] = $row;
    }
}

echo json_encode($certificates);
?>
