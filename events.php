<?php
include_once "./base/header.php";
include_once "./database/db.php";

// Запрос на получение всех мероприятий
$sql = "SELECT * FROM event ORDER BY start_date ASC";
$result = mysqli_query($conn, $sql);

$events = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}
?>

<div class="container">
    <h2 class="text-center mb-4">Мероприятия</h2>
    <div class="row">
        <?php if (empty($events)): ?>
            <div class="col-12">
                <p class="text-center">Нет мероприятий</p>
            </div>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
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
                                <button class="participate-btn">Участвую</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
include_once "./base/footer.php";
?>
