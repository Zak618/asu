<?php
include_once "./base/header.php";
include_once "./database/db.php";
session_start();


// Проверка, авторизован ли пользователь
if (isset($_SESSION['user_id'])) {
    header('Location: profile'); // Перенаправить на страницу профиля или другую страницу
    exit();
}
?>


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="login-form">
                <h2 class="text-center mb-4">Авторизация</h2>
                <?php if (isset($_SESSION['confirmation_message'])): ?>
                    <div class="alert alert-success">
                        <?php 
                            echo $_SESSION['confirmation_message']; 
                            unset($_SESSION['confirmation_message']);
                        ?>
                    </div>
                <?php endif; ?>
                <form id="loginForm" action="./database/login_process.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Войти</button>
                </form>
                <div class="alert alert-danger mt-3 d-none" id="errorMessage"></div>
                <div class="text-center mt-3">
                    <p><a href="forgot_password">Забыли пароль?</a></p>
                    <p>Еще нет аккаунта? <a href="register">Зарегистрироваться</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const errorMessage = document.getElementById('errorMessage');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(form);
        fetch('./database/login_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'error') {
                errorMessage.textContent = data.message;
                errorMessage.classList.remove('d-none');
            } else {
                window.location.href = data.redirect;
            }
        })
        .catch(error => {
            errorMessage.textContent = 'Произошла ошибка. Попробуйте еще раз.';
            errorMessage.classList.remove('d-none');
        });
    });
});
</script>

<?php
include_once "./base/footer.php";
?>
