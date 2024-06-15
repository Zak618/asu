<?php

include_once "db.php";


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT id, first_name, last_name, middle_name, email FROM users WHERE id = ? AND role = 2";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    echo json_encode($teacher);
}
?>
