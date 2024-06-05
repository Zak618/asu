<?php
include_once "db.php";

$sql = "SELECT id, first_name, last_name, middle_name, email, phone_number FROM users WHERE role = 2";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['first_name']}</td>
                <td>{$row['last_name']}</td>
                <td>{$row['middle_name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone_number']}</td>
                <td>
                    <button class='btn btn-warning btn-sm edit-teacher-btn' data-id='{$row['id']}'>Редактировать</button>
                    <button class='btn btn-danger btn-sm delete-teacher-btn' data-id='{$row['id']}'>Удалить</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>Нет преподавателей</td></tr>";
}
?>
