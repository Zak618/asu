<?php
include_once "db.php";

$sql = "SELECT id, item_name, price, color_class FROM market";
$result = $conn->query($sql);

$certificates = [];
while ($row = $result->fetch_assoc()) {
    $certificates[] = $row;
}

echo json_encode($certificates);
?>
