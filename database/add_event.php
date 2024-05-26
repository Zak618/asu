<?php
include_once "db.php";

$title = $_POST['title'];
$description = $_POST['description'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$event_url = isset($_POST['event_url']) ? $_POST['event_url'] : null;
$event_type = $_POST['event_type'];
$event_level = $_POST['event_level'];
$points_winner = isset($_POST['points_winner']) ? $_POST['points_winner'] : 0;
$points_prize = isset($_POST['points_prize']) ? $_POST['points_prize'] : 0;
$points_participant = isset($_POST['points_participant']) ? $_POST['points_participant'] : 0;

if ($event_type == 'Другое') {
    $event_type = $_POST['event_type_other'];
}

if ($event_level == 'Другое') {
    $event_level = $_POST['event_level_other'];
}

$sql = "INSERT INTO event (title, description, start_date, end_date, event_url, event_type, event_level, points_winner, points_prize, points_participant) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssiii", $title, $description, $start_date, $end_date, $event_url, $event_type, $event_level, $points_winner, $points_prize, $points_participant);

if ($stmt->execute()) {
    echo "Мероприятие добавлено успешно!";
} else {
    echo "Ошибка: " . $stmt->error;
}
$stmt->close();

header('Location: ../admin_dashboard.php');
?>
