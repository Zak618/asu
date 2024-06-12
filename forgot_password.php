<?php
include_once "./base/header.php";
include_once "./database/db.php";
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="forgot-password-form">
                <h2 class="text-center mb-4">Сброс пароля</h2>
                <form id="forgotPasswordForm" action="./database/forgot_password_process.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Введите ваш Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Отправить ссылку для сброса</button>
                </form>
                <div class="alert alert-success mt-3 d-none" id="successMessage">
                    Ссылка для сброса пароля была отправлена на ваш email.
                </div>
                <div class="alert alert-danger mt-3 d-none" id="errorMessage">
                    Произошла ошибка. Пожалуйста, попробуйте еще раз.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotPasswordForm');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        fetch('./database/forgot_password_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                successMessage.classList.remove('d-none');
                errorMessage.classList.add('d-none');
            } else {
                successMessage.classList.add('d-none');
                errorMessage.classList.remove('d-none');
            }
        })
        .catch(error => {
            successMessage.classList.add('d-none');
            errorMessage.classList.remove('d-none');
        });
    });
});
</script>

<?php
include_once "./base/footer.php";
?>
