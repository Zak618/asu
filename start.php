<?php

include_once "./base/header.php";
include_once "./database/db.php";

// Здесь идет запрос на ближайшие мероприятия
$sql = "SELECT * FROM event ORDER BY start_date ASC";
$result = mysqli_query($conn, $sql);

$events = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}

$currentYear = date("Y");
?>

<div class="container container-custom">
    <div class="row">
        <div class="col-12 mb-4">
            <h6 class="header-title">Ближайшие мероприятия</h6>
            <h6 class="badge-custom"><?php echo $currentYear; ?></h6>
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

<?php
include_once "./base/footer.php";
?>

