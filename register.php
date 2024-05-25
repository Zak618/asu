<?php
include_once "./base/header.php";
include_once "./database/db.php";
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="registration-form">
                <h2 class="text-center mb-4">Регистрация</h2>
                <form action="./database/register_process.php" method="POST">
                    <div class="mb-3">
                        <label for="firstName" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastName" class="form-label">Фамилия</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                    </div>
                    <div class="mb-3">
                        <label for="middleName" class="form-label">Отчество</label>
                        <input type="text" class="form-control" id="middleName" name="middleName">
                    </div>
                    <div class="mb-3">
                        <label for="groupName" class="form-label">Группа</label>
                        <input type="text" class="form-control" id="groupName" name="groupName" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phoneNumber" class="form-label">Номер телефона</label>
                        <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include_once "./base/footer.php";
?>
