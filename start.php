<?php

include_once "./base/header.php";
include_once "./database/db.php";

// Запрос на ближайшие мероприятия
$sql = "SELECT * FROM event ORDER BY start_date ASC LIMIT 6";
$result = mysqli_query($conn, $sql);

$events = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}

$currentYear = date("Y");

$student_id = $_SESSION['user_id'] ?? null; // Идентификатор студента из сессии

$my_events = [];
if ($student_id) {
    $participation_sql = "SELECT e.* FROM event e JOIN event_participation ep ON e.id = ep.event_id WHERE ep.student_id = ? ORDER BY e.start_date ASC";
    if ($stmt = mysqli_prepare($conn, $participation_sql)) {
        mysqli_stmt_bind_param($stmt, "i", $student_id);
        mysqli_stmt_execute($stmt);
        $participation_result = mysqli_stmt_get_result($stmt);

        if ($participation_result && mysqli_num_rows($participation_result) > 0) {
            while ($row = mysqli_fetch_assoc($participation_result)) {
                $my_events[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}

?>

<div class="container container-custom" style="background-color: #fcfcfc;">
    <div class="row">
        <div class="col-12 mb-4">
            <h6 class="header-title">Ближайшие мероприятия</h6>
            <h6 class="badge-custom1"><?php echo $currentYear; ?></h6>
        </div>
    </div>
    <div id="events-container" class="row">
        <?php if (empty($events)): ?>
            <div class="col-12">
                <p class="text-center">Нет ближайших мероприятий</p>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex">
                    <div class="event-card w-100">
                        <div>
                            <p><strong><?php echo date('d.m.Y H:i', strtotime($event['start_date'])); ?></strong></p>
                            <p><?php echo htmlspecialchars($event['title']); ?></p>
                            <p>Место проведения: <?php echo htmlspecialchars($event['description']); ?></p>
                        </div>
                        <div class="event-footer">
                            <a href="<?php echo htmlspecialchars($event['event_url']); ?>" class="btn btn-light">Подробнее</a>
                            <span class="btn btn-light ml-2">+<?php echo htmlspecialchars($event['points_winner']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 mb-4">
            <div class="card my-events-container p-4">
                <h6 class="my-events-title">Мои события</h6>
                <div class="my-events-list">
                    <?php if (empty($my_events)): ?>
                        <p>Вы еще не участвуете в мероприятиях.</p>
                    <?php else: ?>
                        <ul class="list-unstyled">
                            <?php foreach ($my_events as $event): ?>
                                <a href="event/<?php echo $event['id']; ?>"><li class="event-item"><?php echo htmlspecialchars($event['title']); ?></li></a>
                            <?php endforeach; ?>
                        </ul>
                        <a href="events?tab=my" class="btn btn-light btn-more">Смотреть еще...</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-5 mb-4">
            <div class="telegram-bot-container p-4 text-center">
                <h6 class="telegram-bot-title">TELEGRAM BOT</h6>
                <div class="telegram-bot-content d-flex justify-content-center align-items-center">
                    <img src="./images/baseImage/robot.png" alt="Telegram Bot" class="img-fluid telegram-bot-image">
                    <a href="#" class="btn btn-primary telegram-bot-button ml-3" data-toggle="modal" data-target="#workInProgressModal">Начать</a>
                </div>
                <p class="mt-3">Получайте уведомления о ближайших мероприятиях и узнавайте о своем балансе.</p>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="workInProgressModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Информация</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Мы работаем над этим, совсем скоро появится.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Буду ждать</button>
        </div>
      </div>
    </div>
  </div>

<?php
include_once "./base/footer.php";
?>
