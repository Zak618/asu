<?php
include_once "./base/header.php";
include_once "./database/db.php";
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="login-form">
                <h2 class="text-center mb-4">Авторизация</h2>
                <form action="./database/login_process.php" method="POST">
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
                <div class="text-center mt-3">
                    <p>Еще нет аккаунта? <a href="register">Зарегистрироваться</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once "./base/footer.php";
?>
