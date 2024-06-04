<?php
session_start();

include_once "./database/db.php";

if (!isset($_SESSION['moderator_id'])) {
    header("Location: moderator_login_form.php");
    exit();
}

// Получение списка студентов по статусу
function getStudentsByStatus($status, $conn)
{
    $sql = "SELECT * FROM users WHERE moderator_status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    return $students;
}

// Получение всех мероприятий
function getEvents($conn)
{
    $sql = "SELECT * FROM event";
    $result = $conn->query($sql);
    $events = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
    }
    return $events;
}

// Получение сертификатов по статусу
function getCertificatesByStatus($status, $conn)
{
    $sql = "SELECT c.*, u.first_name, u.last_name, e.title as event_title 
            FROM certificate c 
            JOIN users u ON c.user_id = u.id 
            JOIN event e ON c.event_id = e.id 
            WHERE c.moderator_status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $certificates = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $certificates[] = $row;
        }
    }
    return $certificates;
}

$events = getEvents($conn);

$newStudents = getStudentsByStatus(0, $conn);
$acceptedStudents = getStudentsByStatus(1, $conn);
$rejectedStudents = getStudentsByStatus(2, $conn);

$pendingCertificates = getCertificatesByStatus('на рассмотрении', $conn);
$acceptedCertificates = getCertificatesByStatus('принято', $conn);
$rejectedCertificates = getCertificatesByStatus('отклонено', $conn);

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/admin_dashboard.css">
    <title>Панель модератора</title>
</head>

<body>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Панель модератора</h2>
            <a href="./admin_logout.php" class="btn btn-danger">Выйти</a>
        </div>
        <ul class="nav nav-tabs" id="moderatorTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="events-tab" data-toggle="tab" href="#events" role="tab" aria-controls="events" aria-selected="true">Мероприятия</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="new-tab" data-toggle="tab" href="#new" role="tab" aria-controls="new" aria-selected="false">Новые заявки</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="accepted-tab" data-toggle="tab" href="#accepted" role="tab" aria-controls="accepted" aria-selected="false">Принятые</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="rejected-tab" data-toggle="tab" href="#rejected" role="tab" aria-controls="rejected" aria-selected="false">Отклоненные</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="certificates-tab" data-toggle="tab" href="#certificates" role="tab" aria-controls="certificates" aria-selected="false">Сертификаты</a>
            </li>
        </ul>
        <div class="tab-content" id="studentTabsContent">
            <!-- Мероприятия -->
            <div class="tab-pane fade show active" id="events" role="tabpanel" aria-labelledby="events-tab">
                <div class="row mt-3">
                    <div class="col-12 text-right">
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addEventModal">Добавить мероприятие</button>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <?php foreach ($events as $event) : ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                            <p class="card-text"><strong>Описание:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                                            <p class="card-text"><strong>Дата начала:</strong> <?php echo htmlspecialchars($event['start_date']); ?></p>
                                            <p class="card-text"><strong>Дата окончания:</strong> <?php echo htmlspecialchars($event['end_date']); ?></p>
                                            <p class="card-text"><strong>URL:</strong> <a href="<?php echo htmlspecialchars($event['event_url']); ?>" target="_blank"><?php echo htmlspecialchars($event['event_url']); ?></a></p>
                                            <p class="card-text"><strong>Тип:</strong> <?php echo htmlspecialchars($event['event_type']); ?></p>
                                            <p class="card-text"><strong>Уровень:</strong> <?php echo htmlspecialchars($event['event_level']); ?></p>
                                            <p class="card-text"><strong>Очки победителя:</strong> <?php echo htmlspecialchars($event['points_winner']); ?></p>
                                            <p class="card-text"><strong>Очки за приз:</strong> <?php echo htmlspecialchars($event['points_prize']); ?></p>
                                            <p class="card-text"><strong>Очки за участие:</strong> <?php echo htmlspecialchars($event['points_participant']); ?></p>
                                            <div class="mt-auto text-right">
                                                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $event['id']; ?>">Редактировать</button>
                                                <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $event['id']; ?>">Удалить</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Новые заявки -->
            <div class="tab-pane fade" id="new" role="tabpanel" aria-labelledby="new-tab">
                <div class="row mt-3">
                    <?php foreach ($newStudents as $student) : ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h5>
                                    <p class="card-text">Группа: <?php echo htmlspecialchars($student['group_name']); ?></p>
                                    <p class="card-text">Email: <?php echo htmlspecialchars($student['email']); ?></p>
                                    <p class="card-text">Телефон: <?php echo htmlspecialchars($student['phone_number']); ?></p>
                                    <button class="btn btn-success btn-sm accept-btn" data-id="<?php echo $student['id']; ?>">Принять</button>
                                    <button class="btn btn-danger btn-sm reject-btn" data-id="<?php echo $student['id']; ?>">Отклонить</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Принятые заявки -->
            <div class="tab-pane fade" id="accepted" role="tabpanel" aria-labelledby="accepted-tab">
                <div class="row mt-3">
                    <?php foreach ($acceptedStudents as $student) : ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h5>
                                    <p class="card-text">Группа: <?php echo htmlspecialchars($student['group_name']); ?></p>
                                    <p class="card-text">Email: <?php echo htmlspecialchars($student['email']); ?></p>
                                    <p class="card-text">Телефон: <?php echo htmlspecialchars($student['phone_number']); ?></p>
                                    <p class="card-text">Код направления: <?php echo htmlspecialchars($student['direction_code']); ?></p>
                                    <p class="card-text">Название направления: <?php echo htmlspecialchars($student['direction_name']); ?></p>
                                    <p class="card-text">Профиль: <?php echo htmlspecialchars($student['profile']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Отклоненные заявки -->
            <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                <div class="row mt-3">
                    <?php foreach ($rejectedStudents as $student) : ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h5>
                                    <p class="card-text">Группа: <?php echo htmlspecialchars($student['group_name']); ?></p>
                                    <p class="card-text">Email: <?php echo htmlspecialchars($student['email']); ?></p>
                                    <p class="card-text">Телефон: <?php echo htmlspecialchars($student['phone_number']); ?></p>
                                    <p class="card-text">Комментарий модератора: <?php echo htmlspecialchars($student['moderator_comment']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Модальное окно для показа сертификата -->
<div class="modal fade" id="viewCertificateModal" tabindex="-1" aria-labelledby="viewCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCertificateModalLabel">Сертификат</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="certificateImage" src="" alt="Сертификат" class="img-fluid">
            </div>
        </div>
    </div>
</div>

            <!-- Сертификаты -->
            <div class="tab-pane fade" id="certificates" role="tabpanel" aria-labelledby="certificates-tab">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-pending-tab" data-toggle="pill" href="#pills-pending" role="tab" aria-controls="pills-pending" aria-selected="true">На рассмотрении</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-accepted-tab" data-toggle="pill" href="#pills-accepted" role="tab" aria-controls="pills-accepted" aria-selected="false">Принятые</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-rejected-tab" data-toggle="pill" href="#pills-rejected" role="tab" aria-controls="pills-rejected" aria-selected="false">Отклоненные</a>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-pending" role="tabpanel" aria-labelledby="pills-pending-tab">
                        <div class="row mt-3">
                            <?php foreach ($pendingCertificates as $certificate) : ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($certificate['first_name'] . ' ' . $certificate['last_name']); ?></h5>
                                            <p class="card-text">Мероприятие: <?php echo htmlspecialchars($certificate['event_title']); ?></p>
                                            <p class="card-text">Место: <?php echo htmlspecialchars($certificate['place']); ?></p>
                                            <p class="card-text">Дата загрузки: <?php echo htmlspecialchars($certificate['upload_date']); ?></p>
                                            <button class="btn btn-primary btn-sm view-certificate-btn" data-file-path="<?php echo htmlspecialchars($certificate['file_path']); ?>">Показать сертификат</button>
                                            <button class="btn btn-success btn-sm accept-certificate-btn" data-id="<?php echo $certificate['id']; ?>">Принять</button>
                                            <button class="btn btn-danger btn-sm reject-certificate-btn" data-id="<?php echo $certificate['id']; ?>">Отклонить</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-accepted" role="tabpanel" aria-labelledby="pills-accepted-tab">
                        <div class="row mt-3">
                            <?php foreach ($acceptedCertificates as $certificate) : ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($certificate['first_name'] . ' ' . $certificate['last_name']); ?></h5>
                                            <p class="card-text">Мероприятие: <?php echo htmlspecialchars($certificate['event_title']); ?></p>
                                            <p class="card-text">Место: <?php echo htmlspecialchars($certificate['place']); ?></p>
                                            <p class="card-text">Дата загрузки: <?php echo htmlspecialchars($certificate['upload_date']); ?></p>
                                            <button class="btn btn-primary btn-sm view-certificate-btn" data-file-path="<?php echo htmlspecialchars($certificate['file_path']); ?>">Показать сертификат</button>
                                            <p class="card-text">Начислено баллов: <?php echo htmlspecialchars($certificate['points_awarded']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-rejected" role="tabpanel" aria-labelledby="pills-rejected-tab">
                        <div class="row mt-3">
                            <?php foreach ($rejectedCertificates as $certificate) : ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($certificate['first_name'] . ' ' . $certificate['last_name']); ?></h5>
                                            <p class="card-text">Мероприятие: <?php echo htmlspecialchars($certificate['event_title']); ?></p>
                                            <p class="card-text">Место: <?php echo htmlspecialchars($certificate['place']); ?></p>
                                            <p class="card-text">Дата загрузки: <?php echo htmlspecialchars($certificate['upload_date']); ?></p>
                                            <button class="btn btn-primary btn-sm view-certificate-btn" data-file-path="<?php echo htmlspecialchars($certificate['file_path']); ?>">Показать сертификат</button>
                                            <p class="card-text">Причина отклонения: <?php echo htmlspecialchars($certificate['moderator_comment']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для добавления мероприятия -->
    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEventModalLabel">Добавить мероприятие</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <form id="addEventForm">
                        <div class="mb-3">
                            <label for="eventTitle" class="form-label">Название</label>
                            <input type="text" class="form-control" id="eventTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventDescription" class="form-label">Описание</label>
                            <textarea class="form-control" id="eventDescription" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="eventStartDate" class="form-label">Дата и время начала</label>
                            <input type="datetime-local" class="form-control" id="eventStartDate" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventEndDate" class="form-label">Дата и время окончания</label>
                            <input type="datetime-local" class="form-control" id="eventEndDate" name="end_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="eventUrl" class="form-label">URL</label>
                            <input type="url" class="form-control" id="eventUrl" name="event_url">
                        </div>
                        <div class="mb-3">
                            <label for="eventType" class="form-label">Тип</label>
                            <select class="form-control" id="eventType" name="event_type" required>
                                <option value="Олимпиада">Олимпиада</option>
                                <option value="Чемпионат">Чемпионат</option>
                                <option value="Хакатон">Хакатон</option>
                                <option value="Тренинг">Тренинг</option>
                                <option value="Конференция">Конференция</option>
                                <option value="Другое">Другое</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="eventTypeOther" name="event_type_other" placeholder="Укажите, что именно другое" style="display:none;">
                        </div>
                        <div class="mb-3">
                            <label for="eventLevel" class="form-label">Уровень</label>
                            <select class="form-control" id="eventLevel" name="event_level" required>
                                <option value="Вузовский">Вузовский</option>
                                <option value="Региональный">Региональный</option>
                                <option value="Всероссийский">Всероссийский</option>
                                <option value="Международный">Международный</option>
                                <option value="Другое">Другое</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="eventLevelOther" name="event_level_other" placeholder="Укажите, что именно другое" style="display:none;">
                        </div>
                        <div class="mb-3">
                            <label for="pointsWinner" class="form-label">Очки победителя</label>
                            <input type="number" class="form-control" id="pointsWinner" name="points_winner" required>
                        </div>
                        <div class="mb-3">
                            <label for="pointsPrize" class="form-label">Очки за приз</label>
                            <input type="number" class="form-control" id="pointsPrize" name="points_prize" required>
                        </div>
                        <div class="mb-3">
                            <label for="pointsParticipant" class="form-label">Очки за участие</label>
                            <input type="number" class="form-control" id="pointsParticipant" name="points_participant" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для редактирования мероприятия -->
    <div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEventModalLabel">Редактировать мероприятие</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <form id="editEventForm">
                        <input type="hidden" id="editEventId" name="id">
                        <div class="mb-3">
                            <label for="editEventTitle" class="form-label">Название</label>
                            <input type="text" class="form-control" id="editEventTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEventDescription" class="form-label">Описание</label>
                            <textarea class="form-control" id="editEventDescription" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editEventStartDate" class="form-label">Дата и время начала</label>
                            <input type="datetime-local" class="form-control" id="editEventStartDate" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEventEndDate" class="form-label">Дата и время окончания</label>
                            <input type="datetime-local" class="form-control" id="editEventEndDate" name="end_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEventUrl" class="form-label">URL</label>
                            <input type="url" class="form-control" id="editEventUrl" name="event_url">
                        </div>
                        <div class="mb-3">
                            <label for="editEventType" class="form-label">Тип</label>
                            <select class="form-control" id="editEventType" name="event_type" required>
                                <option value="Олимпиада">Олимпиада</option>
                                <option value="Чемпионат">Чемпионат</option>
                                <option value="Хакатон">Хакатон</option>
                                <option value="Тренинг">Тренинг</option>
                                <option value="Конференция">Конференция</option>
                                <option value="Другое">Другое</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="editEventTypeOther" name="event_type_other" placeholder="Укажите, что именно другое" style="display:none;">
                        </div>
                        <div class="mb-3">
                            <label for="editEventLevel" class="form-label">Уровень</label>
                            <select class="form-control" id="editEventLevel" name="event_level" required>
                                <option value="Вузовский">Вузовский</option>
                                <option value="Региональный">Региональный</option>
                                <option value="Всероссийский">Всероссийский</option>
                                <option value="Международный">Международный</option>
                                <option value="Другое">Другое</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="editEventLevelOther" name="event_level_other" placeholder="Укажите, что именно другое" style="display:none;">
                        </div>
                        <div class="mb-3">
                            <label for="editPointsWinner" class="form-label">Очки победителя</label>
                            <input type="number" class="form-control" id="editPointsWinner" name="points_winner" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPointsPrize" class="form-label">Очки за приз</label>
                            <input type="number" class="form-control" id="editPointsPrize" name="points_prize" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPointsParticipant" class="form-label">Очки за участие</label>
                            <input type="number" class="form-control" id="editPointsParticipant" name="points_participant" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для комментария -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Причина отклонения</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <form id="rejectForm">
                        <div class="mb-3">
                            <label for="rejectComment" class="form-label">Комментарий</label>
                            <textarea class="form-control" id="rejectComment" name="comment" required></textarea>
                        </div>
                        <input type="hidden" id="rejectStudentId" name="student_id">
                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для информации о направлении -->
    <div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="acceptModalLabel">Информация о направлении</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <form id="acceptForm">
                        <div class="mb-3">
                            <label for="directionCode" class="form-label">Код направления</label>
                            <input type="text" class="form-control" id="directionCode" name="direction_code" required>
                        </div>
                        <div class="mb-3">
                            <label for="directionName" class="form-label">Название направления</label>
                            <input type="text" class="form-control" id="directionName" name="direction_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="profile" class="form-label">Профиль</label>
                            <input type="text" class="form-control" id="profile" name="profile" required>
                        </div>
                        <input type="hidden" id="acceptStudentId" name="student_id">
                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
                                <!-- Модальное окно для причины отклонения сертификата -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Причина отклонения</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <div class="mb-3">
                        <label for="rejectComment" class="form-label">Комментарий</label>
                        <textarea class="form-control" id="rejectComment" name="comment" required></textarea>
                    </div>
                    <input type="hidden" id="rejectCertificateId" name="certificate_id">
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
$(document).ready(function() {
    // Показ изображения сертификата
    $('.view-certificate-btn').click(function() {
        var filePath = $(this).data('file-path');
        $('#certificateImage').attr('src', filePath);
        $('#viewCertificateModal').modal('show');
    });

    // Добавление мероприятия
    $('#addEventForm').submit(function(e) {
        e.preventDefault();
        $.post('./database/add_event.php', $(this).serialize(), function(response) {
            $('#addEventModal').modal('hide');
            location.reload();
        });
    });

    // Редактирование мероприятия
    $('.edit-btn').click(function() {
        var eventId = $(this).data('id');
        $.get('./database/get_event.php', {
            id: eventId
        }, function(data) {
            var event = JSON.parse(data);
            $('#editEventId').val(event.id);
            $('#editEventTitle').val(event.title);
            $('#editEventDescription').val(event.description);
            $('#editEventStartDate').val(event.start_date.replace(' ', 'T'));
            $('#editEventEndDate').val(event.end_date.replace(' ', 'T'));
            $('#editEventUrl').val(event.event_url);
            $('#editEventType').val(event.event_type);
            $('#editEventLevel').val(event.event_level);
            $('#editPointsWinner').val(event.points_winner);
            $('#editPointsPrize').val(event.points_prize);
            $('#editPointsParticipant').val(event.points_participant);
            $('#editEventModal').modal('show');

            // Показать поле "Другое" для типа мероприятия, если выбрано "Другое"
            if (event.event_type === 'Другое') {
                $('#editEventTypeOther').val(event.event_type).show();
            } else {
                $('#editEventTypeOther').hide();
            }

            // Показать поле "Другое" для уровня мероприятия, если выбрано "Другое"
            if (event.event_level === 'Другое') {
                $('#editEventLevelOther').val(event.event_level).show();
            } else {
                $('#editEventLevelOther').hide();
            }
        });
    });

    $('#editEventForm').submit(function(e) {
        e.preventDefault();
        $.post('./database/edit_event.php', $(this).serialize(), function(response) {
            $('#editEventModal').modal('hide');
            location.reload();
        });
    });

    // Удаление мероприятия
    $('.delete-btn').click(function() {
        if (confirm('Вы уверены, что хотите удалить это мероприятие?')) {
            var eventId = $(this).data('id');
            $.post('./database/delete_event.php', {
                id: eventId
            }, function(response) {
                location.reload();
            });
        }
    });

    // Показать/скрыть поле "Другое" для типа мероприятия
    $('#eventType').change(function() {
        if ($(this).val() === 'Другое') {
            $('#eventTypeOther').show();
        } else {
            $('#eventTypeOther').hide();
        }
    });

    // Показать/скрыть поле "Другое" для уровня мероприятия
    $('#eventLevel').change(function() {
        if ($(this).val() === 'Другое') {
            $('#eventLevelOther').show();
        } else {
            $('#eventLevelOther').hide();
        }
    });

    // Показать/скрыть поле "Другое" для редактирования типа мероприятия
    $('#editEventType').change(function() {
        if ($(this).val() === 'Другое') {
            $('#editEventTypeOther').show();
        } else {
            $('#editEventTypeOther').hide();
        }
    });

    // Показать/скрыть поле "Другое" для редактирования уровня мероприятия
    $('#editEventLevel').change(function() {
        if ($(this).val() === 'Другое') {
            $('#editEventLevelOther').show();
        } else {
            $('#editEventLevelOther').hide();
        }
    });

    $('.accept-certificate-btn').click(function() {
        var certificateId = $(this).data('id');
        updateCertificateStatus(certificateId, 'принято');
    });

    $('.reject-certificate-btn').click(function() {
        var certificateId = $(this).data('id');
        $('#rejectCertificateId').val(certificateId);
        $('#rejectModal').modal('show');
    });

    $('#rejectForm').submit(function(e) {
        e.preventDefault();
        var certificateId = $('#rejectCertificateId').val();
        var comment = $('#rejectComment').val();
        updateCertificateStatus(certificateId, 'отклонено', comment);
    });

    function updateCertificateStatus(certificateId, status, comment = '') {
        $.post('./database/update_certificate_status.php', {
            certificate_id: certificateId,
            status: status,
            comment: comment
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Произошла ошибка: ' + response.error);
            }
        }, 'json');
    }
});
</script>

</script>



</body>

</html>
