<?php
include_once "./base/header.php";
include_once "./database/db.php";


$sql = "SELECT * FROM event ORDER BY start_date ASC";
$result = mysqli_query($conn, $sql);

$events = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}


$moderator_status = $_SESSION['moderator_status']; // Статус модератора из сессии

$student_id = $_SESSION['user_id']; // Идентификатор студента из сессии
$participation_sql = "SELECT event_id FROM event_participation WHERE student_id = $student_id";
$participation_result = mysqli_query($conn, $participation_sql);

$participated_events = [];
if ($participation_result && mysqli_num_rows($participation_result) > 0) {
    while ($row = mysqli_fetch_assoc($participation_result)) {
        $participated_events[] = $row['event_id'];
    }
}
?>

<div class="container">
    <h2 class="text-center mb-4">Мероприятия</h2>
    <div class="row">
        <?php if (empty($events)) : ?>
            <div class="col-12">
                <p class="text-center">Нет мероприятий</p>
            </div>
        <?php else : ?>
            <?php foreach ($events as $event) : ?>
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="ticket-container">
                        <div class="ticket-card">
                            <div class="circle-top"></div>
                            <div class="circle-bottom"></div>
                            <div class="ticket-header">
                                <h5><?php echo htmlspecialchars($event['title']); ?></h5>
                                <span class="badge badge-custom"><?php echo htmlspecialchars($event['event_level']); ?></span>
                            </div>
                            <div class="ticket-body">
                                <p><strong>Описание:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                                <p><strong>Начало:</strong> <?php echo date('d.m.Y H:i', strtotime($event['start_date'])); ?></p>
                                <p><strong>Окончание:</strong> <?php echo date('d.m.Y H:i', strtotime($event['end_date'])); ?></p>
                                <p><strong>Тип:</strong> <?php echo htmlspecialchars($event['event_type']); ?></p>
                                <p><strong>URL:</strong> <a class="event-url" href="<?php echo htmlspecialchars($event['event_url']); ?>" target="_blank"><?php echo htmlspecialchars($event['event_url']); ?></a></p>
                            </div>
                            <div class="ticket-footer">
                                <span><strong>Монет победителю:</strong> <?php echo htmlspecialchars($event['points_winner']); ?></span>
                                <span><strong>Монет призёру:</strong> <?php echo htmlspecialchars($event['points_prize']); ?></span>
                                <span><strong>Монет участнику:</strong> <?php echo htmlspecialchars($event['points_participant']); ?></span>
                                <?php if (in_array($event['id'], $participated_events)) : ?>
                                    <div class="btn-group">
                                        <button class="btn cancel-btn" data-event-id="<?php echo $event['id']; ?>">Отменить</button>
                                        <button class="btn certificate-btn" data-event-id="<?php echo $event['id']; ?>">Загрузить</button>
                                    </div>

                                <?php else : ?>
                                    <button class="participate-btn" data-event-id="<?php echo $event['id']; ?>" <?php echo ($moderator_status != 1) ? 'disabled' : ''; ?>>Участвую</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const participateButtons = document.querySelectorAll('.participate-btn');
        const cancelButtons = document.querySelectorAll('.cancel-btn');

        participateButtons.forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.getAttribute('data-event-id');
                handleParticipation(eventId, 'participate');
            });
        });

        cancelButtons.forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.getAttribute('data-event-id');
                handleParticipation(eventId, 'cancel');
            });
        });

        function handleParticipation(eventId, action) {
            fetch('./database/participate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        action: action
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Произошла ошибка: ' + data.error);
                    }
                });
        }
    });
</script>



<?php
include_once "./base/footer.php";
?>