<?php
include_once "./base/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<h2 class="text-center mb-4"><span style="color: #ffcc00;">М</span>аркет</h2>
<div class="container mt-4">
    <div class="nav-container">
        <ul class="nav nav-pills mb-3" id="marketTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="all-certificates-tab" data-toggle="pill" href="#all-certificates" role="tab" aria-controls="all-certificates" aria-selected="true">Все купоны</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="exchange-history-tab" data-toggle="pill" href="#exchange-history" role="tab" aria-controls="exchange-history" aria-selected="false">История обмена</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="my-certificates-tab" data-toggle="pill" href="#my-certificates" role="tab" aria-controls="my-certificates" aria-selected="false">Мои купоны</a>
            </li>
        </ul>
    </div>
    <div class="tab-content" id="marketTabsContent">
        <div class="tab-pane fade show active" id="all-certificates" role="tabpanel" aria-labelledby="all-certificates-tab">
            <div class="card-container" id="certificatesList">
                <!-- Сертификаты будут загружены здесь -->
            </div>
        </div>
        <div class="tab-pane fade" id="exchange-history" role="tabpanel" aria-labelledby="exchange-history-tab">
            <div class="card-container history" id="exchangeHistoryList">
                <!-- История обмена будет загружена здесь -->
            </div>
        </div>

        <div class="tab-pane fade" id="my-certificates" role="tabpanel" aria-labelledby="my-certificates-tab">
            <div class="card-container" id="myCertificatesList">
                <!-- Мои сертификаты будут загружены здесь -->
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для подтверждения покупки -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="purchaseModalLabel">Подтверждение покупки</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите приобрести этот купон?</p>
                <input type="hidden" id="itemId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-warning" id="confirmPurchaseBtn">Подтвердить</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для активации купона -->
<div class="modal fade" id="activateModal" tabindex="-1" aria-labelledby="activateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activateModalLabel">Активация купона</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Выберите преподавателя для активации купона:</p>
                <select id="teacherSelect" class="form-control">
                    <!-- Преподаватели будут загружены здесь -->
                </select>
                <input type="hidden" id="couponId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-warning" id="confirmActivateBtn">Активировать</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        // Загрузка всех сертификатов
        $.ajax({
            url: './database/get_certificates.php',
            method: 'GET',
            success: function(data) {
                const certificates = JSON.parse(data);
                certificates.forEach(cert => {
                    $('#certificatesList').append(`
                        <div class="certificate-card ${cert.color_class}">
                            <div>${cert.item_name}</div>
                            <div class="dashed-line"></div>
                            <div class="price-button-wrapper">
                                <button class="price-button" data-id="${cert.id}" data-price="${cert.price}">${cert.price} баллов</button>
                            </div>
                        </div>
                    `);
                });

                // Добавить обработчик события на кнопки с ценой
                $('.price-button').click(function() {
                    const itemId = $(this).data('id');
                    $('#itemId').val(itemId);
                    $('#purchaseModal').modal('show');
                });
            },
            error: function(err) {
                console.error('Error loading certificates:', err);
            }
        });

        // Загрузка сертификатов пользователя
        $.ajax({
            url: './database/get_my_certificates.php',
            method: 'GET',
            data: {
                user_id: <?php echo $user_id; ?>
            },
            success: function(data) {
                const myCertificates = JSON.parse(data);
                myCertificates.forEach(cert => {
                    const isActive = cert.status === 'активно';
                    const cardClass = isActive ? 'certificate-card' : 'certificate-card inactive';
                    const button = isActive ? `<button class="btn btn-warning activate-button" data-coupon-id="${cert.coupon_id}">Активировать</button>` : '<span class="activated-text">Активирован</span>';

                    $('#myCertificatesList').append(`
                        <div class="${cardClass} ${cert.color_class}">
                            <div>${cert.item_name}</div>
                            <div class="dashed-line"></div>
                            <div class="price-button-wrapper">
                                ${button}
                            </div>
                        </div>
                    `);
                });

                // Добавить обработчик события на кнопки активации
                $('.activate-button').click(function() {
                    const couponId = $(this).data('coupon-id');
                    $('#couponId').val(couponId);

                    // Загрузка преподавателей
                    $.ajax({
                        url: './database/fetch_teachers.php',
                        method: 'GET',
                        success: function(data) {
                            const teachers = JSON.parse(data);
                            $('#teacherSelect').empty();
                            teachers.forEach(teacher => {
                                $('#teacherSelect').append(`<option value="${teacher.id}">${teacher.first_name} ${teacher.last_name}</option>`);
                            });
                            $('#activateModal').modal('show');
                        },
                        error: function(err) {
                            console.error('Error loading teachers:', err);
                        }
                    });
                });
            },
            error: function(err) {
                console.error('Error loading my certificates:', err);
            }
        });

        // Обработка подтверждения покупки
        $('#confirmPurchaseBtn').click(function() {
            const itemId = $('#itemId').val();
            $.ajax({
                url: './database/add_purchase.php',
                method: 'POST',
                data: {
                    item_id: itemId
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#purchaseModal').modal('hide');
                        alert('Купон успешно приобретен!');
                        updateBalance(result.new_balance);
                        // Перезагрузите список сертификатов
                        loadMyCertificates();
                    } else {
                        alert('Ошибка при покупке купона: ' + result.message);
                    }
                },
                error: function(err) {
                    console.error('Error making purchase:', err);
                    alert('Произошла ошибка при обработке покупки.');
                }
            });
        });

        // Функция для форматирования даты на русский
        function formatDateTime(dateString) {
            const date = new Date(dateString);
    date.setHours(date.getHours() + 3); // Добавляем 3 часа для московского времени
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('ru-RU', options);
}


    // Загрузка истории обмена
    $.ajax({
        url: './database/fetch_exchange_history.php',
        method: 'GET',
        data: { user_id: <?php echo $user_id; ?> },
        success: function(data) {
            const exchangeHistory = JSON.parse(data);
            exchangeHistory.forEach(entry => {
                const operationType = entry.teacher_name 
                    ? `<i class="icon fas fa-check-circle"></i>Активировано у: ${entry.teacher_name}` 
                    : `<i class="icon fas fa-shopping-cart"></i>Куплено`;
                const cardClass = entry.status === 'активно' ? 'exchange-card' : 'exchange-card inactive';
                
                $('#exchangeHistoryList').append(`
                    <div class="${cardClass}">
                        <h5><i class="icon fas fa-ticket-alt"></i>${entry.item_name}</h5>
                        <p class="date">Дата и время: ${formatDateTime(entry.purchase_date)}</p>
                        <span class="badge-custom">${entry.status}</span>
                        <p class="operation-type">${operationType}</p>
                    </div>
                `);
            });
        },
        error: function(err) {
            console.error('Error loading exchange history:', err);
        }
    });
        // Обработка подтверждения активации
        $('#confirmActivateBtn').click(function() {
            const couponId = $('#couponId').val();
            const teacherId = $('#teacherSelect').val();
            $.ajax({
                url: './database/activate_certificate.php',
                method: 'POST',
                data: {
                    coupon_id: couponId,
                    teacher_id: teacherId
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#activateModal').modal('hide');
                        alert('Купон успешно активирован!');
                        // Перезагрузите список сертификатов
                        loadMyCertificates();
                    } else {
                        alert('Ошибка при активации купона: ' + result.message);
                    }
                },
                error: function(err) {
                    console.error('Error activating certificate:', err);
                    alert('Произошла ошибка при активации купона.');
                }
            });
        });

        // Функция для загрузки сертификатов пользователя
        function loadMyCertificates() {
            $.ajax({
                url: './database/get_my_certificates.php',
                method: 'GET',
                data: {
                    user_id: <?php echo $user_id; ?>
                },
                success: function(data) {
                    const myCertificates = JSON.parse(data);
                    $('#myCertificatesList').empty();
                    myCertificates.forEach(cert => {
                        const isActive = cert.status === 'активно';
                        const cardClass = isActive ? 'certificate-card' : 'certificate-card inactive';
                        const button = isActive ? `<button class="btn btn-warning activate-button" data-coupon-id="${cert.coupon_id}">Активировать</button>` : '<span class="activated-text">Активирован</span>';

                        $('#myCertificatesList').append(`
                            <div class="${cardClass} ${cert.color_class}">
                                <div>${cert.item_name}</div>
                                <div class="dashed-line"></div>
                                <div class="price-button-wrapper">
                                    ${button}
                                </div>
                            </div>
                        `);
                    });

                    // Добавить обработчик события на кнопки активации
                    $('.activate-button').click(function() {
                        const couponId = $(this).data('coupon-id');
                        $('#couponId').val(couponId);

                        // Загрузка преподавателей
                        $.ajax({
                            url: './database/fetch_teachers.php',
                            method: 'GET',
                            success: function(data) {
                                const teachers = JSON.parse(data);
                                $('#teacherSelect').empty();
                                teachers.forEach(teacher => {
                                    $('#teacherSelect').append(`<option value="${teacher.id}">${teacher.first_name} ${teacher.last_name}</option>`);
                                });
                                $('#activateModal').modal('show');
                            },
                            error: function(err) {
                                console.error('Error loading teachers:', err);
                            }
                        });
                    });
                },
                error: function(err) {
                    console.error('Error loading my certificates:', err);
                }
            });
        }

        // Изначальная загрузка сертификатов пользователя
        loadMyCertificates();

        function updateBalance(newBalance) {
            $('.balance-text').text(newBalance);
        }
    });
</script>

<?php
include_once "./base/footer.php";
?>