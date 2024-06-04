<?php
include_once "./base/header.php";
include_once "./database/db.php";

// Получаем активную вкладку из GET-параметра, если она не установлена, по умолчанию показываем все мероприятия
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';

$student_id = $_SESSION['user_id']; // Идентификатор студента из сессии

// SQL-запрос в зависимости от активной вкладки
if ($active_tab == 'my') {
    $sql = "SELECT e.* FROM event e 
            JOIN event_participation ep ON e.id = ep.event_id 
            WHERE ep.student_id = $student_id 
            ORDER BY e.start_date ASC";
} else {
    $sql = "SELECT * FROM event ORDER BY start_date ASC";
}

$result = mysqli_query($conn, $sql);

$events = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}

$moderator_status = $_SESSION['moderator_status']; // Статус модератора из сессии

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
    <div class="mb-4">
        <a href="?tab=all" class="btn btn-outline-primary <?php echo $active_tab == 'all' ? 'active' : ''; ?>">Все мероприятия</a>
        <a href="?tab=my" class="btn btn-outline-primary <?php echo $active_tab == 'my' ? 'active' : ''; ?>">Мои мероприятия</a>
    </div>
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
                                        <a href="event.php?id=<?php echo $event['id']; ?>" class="btn certificate-btn">Подробнее</a>
                                    </div>
                                <?php else : ?>
                                    <button class="btn participate-btn" data-event-id="<?php echo $event['id']; ?>">Участвую</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно -->
<div class="modal fade" id="moderatorModal" tabindex="-1" role="dialog" aria-labelledby="moderatorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moderatorModalLabel">Информация</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Ваши данные еще не проверены.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Ок</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const participateButtons = document.querySelectorAll('.participate-btn');
        const cancelButtons = document.querySelectorAll('.cancel-btn');

        participateButtons.forEach(button => {
            button.addEventListener('click', function() {
                <?php if ($moderator_status != 1): ?>
                    $('#moderatorModal').modal('show');
                <?php else: ?>
                    const eventId = this.getAttribute('data-event-id');
                    handleParticipation(eventId, 'participate');
                <?php endif; ?>
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
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>

<?php
include_once "./base/footer.php";
?>
