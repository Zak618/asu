<?php
include_once "db.php";

$student_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

$query = "SELECT MONTH(participation_date) AS month, COUNT(*) AS participation_count 
          FROM event_participation 
          WHERE student_id = ? 
          GROUP BY MONTH(participation_date)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>
