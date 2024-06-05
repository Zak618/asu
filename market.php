<?php
include_once "./base/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
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
            <!-- История обмена -->
        </div>
        <div class="tab-pane fade" id="my-certificates" role="tabpanel" aria-labelledby="my-certificates-tab">
            <!-- Мои купоны -->
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        // Загрузка сертификатов из базы данных
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
                    const itemId = $(this).attr('data-id');
                    $('#itemId').val(itemId);
                    $('#purchaseModal').modal('show');
                });
            },
            error: function(err) {
                console.error('Error loading certificates:', err);
            }
        });

        // Обработка подтверждения покупки
        $('#confirmPurchaseBtn').click(function() {
            const itemId = $('#itemId').val();
            $.ajax({
                url: './database/add_purchase.php',
                method: 'POST',
                data: { item_id: itemId },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#purchaseModal').modal('hide');
                        alert('Купон успешно приобретен!');
                        updateBalance(result.new_balance);
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

        function updateBalance(newBalance) {
            $('.balance-text').text(newBalance);
        }
    });
    
</script>

<?php
include_once "./base/footer.php";
?>
