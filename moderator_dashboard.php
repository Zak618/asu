<?php
session_start();

include_once "./database/db.php";

if (!isset($_SESSION['moderator_id']) || $_SESSION['role'] != 4) {
    header("Location: admin");
    exit();
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
            <a class="nav-link active" id="certificates-tab" data-toggle="tab" href="#certificates" role="tab" aria-controls="certificates" aria-selected="true">Сертификаты</a>
        </li>
    </ul>
    <div class="tab-content" id="moderatorTabsContent">
        <!-- Сертификаты -->
        <div class="tab-pane fade show active" id="certificates" role="tabpanel" aria-labelledby="certificates-tab">
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

            // Принятие сертификата
            $('.accept-certificate-btn').click(function() {
                var certificateId = $(this).data('id');
                updateCertificateStatus(certificateId, 'принято');
            });

            // Отклонение сертификата
            $('.reject-certificate-btn').click(function() {
                var certificateId = $(this).data('id');
                $('#rejectCertificateId').val(certificateId);
                $('#rejectCertificateModal').modal('show');
            });

            $('#rejectCertificateForm').submit(function(e) {
                e.preventDefault();
                var certificateId = $('#rejectCertificateId').val();
                var comment = $('#rejectCertificateComment').val();
                updateCertificateStatus(certificateId, 'отклонено', comment);
            });

            // Функция для обновления статуса сертификата
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

    <!-- Модальное окно для отклонения сертификата -->
    <div class="modal fade" id="rejectCertificateModal" tabindex="-1" aria-labelledby="rejectCertificateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectCertificateModalLabel">Причина отклонения сертификата</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="rejectCertificateForm">
                        <div class="mb-3">
                            <label for="rejectCertificateComment" class="form-label">Комментарий</label>
                            <textarea class="form-control" id="rejectCertificateComment" name="comment" required></textarea>
                        </div>
                        <input type="hidden" id="rejectCertificateId" name="certificate_id">
                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
