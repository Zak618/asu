<?php
include_once "./base/header.php";
include_once "./database/db.php";

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'><i class='fas fa-exclamation-triangle'></i> Пожалуйста, авторизуйтесь для просмотра мероприятия!</div></div>";
    include_once "./base/footer.php";
    exit;
}

if (!isset($_GET['id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'><i class='fas fa-exclamation-circle'></i> Мероприятие не найдено!</div></div>";
    include_once "./base/footer.php";
    exit;
}

$event_id = intval($_GET['id']);

$sql = "SELECT * FROM event WHERE id = $event_id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $event = mysqli_fetch_assoc($result);
} else {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'><i class='fas fa-exclamation-circle'></i> Мероприятие не найдено!</div></div>";
    include_once "./base/footer.php";
    exit;
}

$student_id = $_SESSION['user_id']; // Идентификатор студента из сессии

// Проверка участия пользователя в мероприятии
$participation_sql = "SELECT * FROM event_participation WHERE student_id = $student_id AND event_id = $event_id";
$participation_result = mysqli_query($conn, $participation_sql);
$is_participating = mysqli_num_rows($participation_result) > 0;

// Проверка наличия загруженного сертификата
$certificate_sql = "SELECT * FROM certificate WHERE user_id = $student_id AND event_id = $event_id";
$certificate_result = mysqli_query($conn, $certificate_sql);
$certificate = mysqli_fetch_assoc($certificate_result);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-success text-white text-center py-4">
                    <h2 class="mb-0"><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($event['title']); ?></h2>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-12">
                            <p><strong><i class="fas fa-info-circle"></i> Описание:</strong> <?php echo htmlspecialchars($event['description']); ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-calendar-alt"></i> Начало:</strong> <?php echo date('d.m.Y H:i', strtotime($event['start_date'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-calendar-alt"></i> Окончание:</strong> <?php echo date('d.m.Y H:i', strtotime($event['end_date'])); ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-tag"></i> Тип:</strong> <?php echo htmlspecialchars($event['event_type']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-level-up-alt"></i> Уровень:</strong> <?php echo htmlspecialchars($event['event_level']); ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-trophy"></i> Монет победителю:</strong> <?php echo htmlspecialchars($event['points_winner']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-medal"></i> Монет призёру:</strong> <?php echo htmlspecialchars($event['points_prize']); ?></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-user"></i> Монет участнику:</strong> <?php echo htmlspecialchars($event['points_participant']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-link"></i> URL:</strong> <a href="<?php echo htmlspecialchars($event['event_url']); ?>" target="_blank"><?php echo htmlspecialchars($event['event_url']); ?></a></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-right d-flex flex-wrap justify-content-end">
                    <?php if ($is_participating) : ?>
                        <?php if ($certificate) : ?>
                            <?php if ($certificate['moderator_status'] == 'принято') : ?>
                                <p class="mr-2"><i class="fas fa-check-circle"></i> Статус: Принято. Начислено баллов: <?php echo htmlspecialchars($certificate['points_awarded']); ?></p>
                            <?php elseif ($certificate['moderator_status'] == 'отклонено') : ?>
                                <div class="d-flex align-items-center">
                                    <p class="mr-2"><i class="fas fa-times-circle"></i> Статус: Отклонено. Причина: <?php echo htmlspecialchars($certificate['moderator_comment']); ?></p>
                                    <button class="btn btn-danger delete-certificate-btn m-2" data-certificate-id="<?php echo $certificate['id']; ?>"><i class="fas fa-trash-alt"></i> Удалить сертификат</button>
                                </div>
                            <?php else : ?>
                                <div class="d-flex align-items-center">
                                    <p class="mr-2 mb-0"><i class="fas fa-hourglass-half"></i> Статус: На рассмотрении</p>
                                    <button class="btn btn-danger delete-certificate-btn m-2" data-certificate-id="<?php echo $certificate['id']; ?>"><i class="fas fa-trash-alt"></i> Удалить сертификат</button>
                                </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <button class="btn btn-primary certificate-btn m-2" data-event-id="<?php echo $event_id; ?>" data-toggle="modal" data-target="#uploadModal"><i class="fas fa-file-upload"></i> Загрузить сертификат</button>
                            <button class="btn btn-danger cancel-btn m-2" data-event-id="<?php echo $event_id; ?>"><i class="fas fa-times-circle"></i> Отменить участие</button>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class='alert alert-info text-center w-100'><i class='fas fa-info-circle'></i> Вы еще не участвуете в этом мероприятии.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для загрузки сертификата -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Загрузить сертификат</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadCertificateForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="certificateImage">Выберите изображение сертификата</label>
                        <input type="file" class="form-control-file" id="certificateImage" name="certificateImage" required>
                    </div>
                    <div class="form-group">
                        <label for="place">Место</label>
                        <select class="form-control" id="place" name="place" required>
                            <option value="участник">Участник</option>
                            <option value="призер">Призёр</option>
                            <option value="победитель">Победитель</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                    <button type="submit" class="btn btn-primary">Загрузить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно для удаления сертификата -->
<div class="modal fade" id="deleteCertificateModal" tabindex-1" role="dialog" aria-labelledby="deleteCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCertificateModalLabel">Удалить сертификат</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить этот сертификат?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteCertificate">Удалить</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cancelButton = document.querySelector('.cancel-btn');
        const certificateButton = document.querySelector('.certificate-btn');
        const deleteCertificateButtons = document.querySelectorAll('.delete-certificate-btn');
        const uploadCertificateForm = document.getElementById('uploadCertificateForm');
        let certificateIdToDelete;

        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                const eventId = this.getAttribute('data-event-id');
                cancelParticipation(eventId);
            });
        }

        deleteCertificateButtons.forEach(button => {
            button.addEventListener('click', function() {
                certificateIdToDelete = this.getAttribute('data-certificate-id');
                console.log("Deleting certificate with ID: ", certificateIdToDelete); // Debugging log
                $('#deleteCertificateModal').modal('show');
            });
        });

        document.getElementById('confirmDeleteCertificate').addEventListener('click', function() {
            console.log("Confirm delete certificate with ID: ", certificateIdToDelete); // Debugging log
            deleteCertificate(certificateIdToDelete);
        });

        uploadCertificateForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(uploadCertificateForm);
            formData.append('event_id', '<?php echo $event_id; ?>');
            formData.append('student_id', '<?php echo $student_id; ?>');

            fetch('/database/upload_certificate.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Сертификат успешно загружен!');
                        $('#uploadModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Произошла ошибка: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        function cancelParticipation(eventId) {
            fetch('/database/participate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        action: 'cancel'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '../events';
                    } else {
                        alert('Произошла ошибка: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function deleteCertificate(certificateId) {
            console.log("Sending delete request for certificate ID: ", certificateId); // Debugging log
            fetch('/database/delete_certificate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        certificate_id: certificateId // Передача certificate_id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Сертификат успешно удалён!');
                        $('#deleteCertificateModal').modal('hide');
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
