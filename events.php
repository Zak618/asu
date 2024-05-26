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

<style>
    .ticket-card {
        border-radius: 10px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .ticket-card:hover {
        transform: scale(1.05);
    }

    .ticket-card::before,
    .ticket-card::after {
        content: "";
        position: absolute;
        width: 20px;
        height: 20px;
        background: #fff;
        border-radius: 50%;
        top: 129px; /* Расположение полукруга */
        transform: translateY(-50%);
    }

    .ticket-card::before {
        left: -10px;
    }

    .ticket-card::after {
        right: -10px;
    }

    .ticket-header {
        background-color: #ff5f5f;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        border-bottom: 2px solid #e0e0e0;
        height: 130px; /* Фиксированная высота для заголовка */
    }

    .ticket-body {
        padding: 20px;
        background-color: #adb5bd;
        flex-grow: 1;
    }

    .ticket-footer {
        background-color: #f8f9fa;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 2px solid #e0e0e0;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .ticket-footer span {
        display: block;
        margin: 5px 0;
    }

    .badge-custom {
        background-color: #ff5f5f;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .event-url {
        word-break: break-all;
        color: #007bff;
        text-decoration: none;
    }

    .event-url:hover {
        text-decoration: underline;
    }

    .ticket-container {
        height: 100%;
        display: flex;
    }
</style>

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
                                <span><strong>Очки победителя:</strong> <?php echo htmlspecialchars($event['points_winner']); ?></span>
                                <span><strong>Очки за приз:</strong> <?php echo htmlspecialchars($event['points_prize']); ?></span>
                                <span><strong>Очки за участие:</strong> <?php echo htmlspecialchars($event['points_participant']); ?></span>
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
