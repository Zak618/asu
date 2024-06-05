<?php
include_once "db.php";

$sql = "SELECT id, first_name, last_name FROM users WHERE role = 2";
$result = $conn->query($sql);

$teachers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
}

echo json_encode($teachers);
?>
