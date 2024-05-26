<?php
include_once "db.php";

$id = $_GET['id'];

$sql = "SELECT * FROM event WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

echo json_encode($event);
