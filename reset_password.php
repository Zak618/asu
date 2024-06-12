<?php
include_once "./base/header.php";
include_once "./database/db.php";

$token = $_GET['token'] ?? '';

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="reset-password-form">
                <h2 class="text-center mb-4">Сброс пароля</h2>
                <form id="resetPasswordForm" action="./database/reset_password_process.php" method="POST">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Новый пароль</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Подтвердите новый пароль</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Сбросить пароль</button>
                </form>
                <div class="alert alert-success mt-3 d-none" id="successMessage">
                    Пароль был успешно сброшен. <a href="login.php">Войти</a>.
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
    const form = document.getElementById('resetPasswordForm');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        fetch('./database/reset_password_process.php', {
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
