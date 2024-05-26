<?php
include_once "db.php";

$id = $_POST['id'];

$sql = "DELETE FROM event WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header('Location: ../admin_dashboard.php');
