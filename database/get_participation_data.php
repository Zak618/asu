<?php
include_once "db.php";

$query = "SELECT MONTH(participation_date) AS month, COUNT(*) AS participation_count 
          FROM event_participation 
          GROUP BY MONTH(participation_date)";
$result = mysqli_query($conn, $query);

$data = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>
